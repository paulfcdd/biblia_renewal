<?php


namespace App\Command\DatabaseMapper;


use App\Entity\Book;
use App\Entity\BookAudioSource;
use App\Entity\BookGroup;
use App\Entity\BookTitleVariant;
use Symfony\Component\Console\Helper\ProgressBar;

class BookMapper extends AbstractMapper
{
    /**
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run()
    {
        $sourceTableData = $this->getSourceTableData('books');
        $this->output->writeln('<info>ИМПОРТ КНИГ</info>');
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        foreach ($sourceTableData as $singeItem) {
            $book = $this->importBook($singeItem);

            if ($book) {
                $this->importBookTitleVariant($book, $singeItem);
                $this->importBookAudioSource($book, $singeItem);
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    /**
     * @param array $sourceBook
     * @return Book
     * @throws \Doctrine\DBAL\DBALException
     */
    private function importBook(array $sourceBook)
    {
        if ($this->getMappedBookGroup($sourceBook)) {
            $query = "INSERT INTO ".$this->targetDb.".book VALUES (
                    NULL, 
                    '".str_replace("'", "''", $this->getMappedBookGroup($sourceBook)['id'])."' ,
                    '".str_replace("'", "''", $sourceBook['title'])."', 
                    '".str_replace("'", "''", $sourceBook['url_title'])."', 
                    '".str_replace("'", "''", $sourceBook['cloud_title'])."', 
                    '".str_replace("'", "''", $sourceBook['canonical'])."', 
                    '".str_replace("'", "''", $sourceBook['inscription'])."', 
                    NULL, 
                    NULL 
                    )";
            $this->connection->prepare($query)->execute();

            $this->fillTmpTableMap('book', $sourceBook['id'], $this->connection->lastInsertId());

            return $this->connection->lastInsertId();
        }

        return null;
    }

    /**
     * @param $sourceBook
     * @return BookGroup|object|null
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getMappedBookGroup($sourceBook)
    {
        $newId = $this->getNewIdFromTmpTableMap('book_group', $sourceBook['group_id']);

        if (!is_bool($newId)) {
            $query = 'SELECT id FROM ' . $this->targetDb . '.book_group WHERE id=' . $newId['new_id'] . ';';

            return  $this->connection->query($query)->fetch();
        }

        return null;
    }

    /**
     * @param Book $importedBook
     * @param array $sourceBook
     * @return $this
     */
    private function importBookTitleVariant(int $importedBook, array $sourceBook)
    {
        $query = "INSERT INTO ".$this->targetDb.".book_title_variant VALUES (
                    NULL, 
                    '".str_replace("'", "''", $importedBook)."', 
                    '".str_replace("'", "''", $sourceBook['main_title'])."', 
                    '".str_replace("'", "''", $sourceBook['chapter_title'])."', 
                    '".str_replace("'", "''", $sourceBook['page_title'])."' 
                    )";
        $this->connection->prepare($query)->execute();


        return $this;
    }


    /**
     * @param Book $importedBook
     * @param array $sourceBook
     * @return $this
     * @throws \Doctrine\DBAL\DBALException
     */
    private function importBookAudioSource(int $importedBook, array $sourceBook)
    {
        $query = "INSERT INTO ".$this->targetDb.".book_audio_source VALUES (
                    NULL, 
                    '".str_replace("'", "''", $importedBook)."', 
                    '".str_replace("'", "''", $sourceBook['audio_source'])."' 
                    )";
        $this->connection->prepare($query)->execute();

        return $this;
    }
}
