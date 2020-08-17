<?php


namespace App\Command\DatabaseMapper;


use App\Entity\Book;
use App\Entity\BookTranslation;
use App\Entity\Lang;
use Symfony\Component\Console\Helper\ProgressBar;

class BookTranslationMapper extends AbstractMapper
{
    /**
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run()
    {
        $sourceTableData = $this->getSourceTableData('books_titles');
        $this->output->writeln('<info>ИМПОРТ ПЕРЕВОДОВ ЗАГОЛОВКОВ КНИГ</info>');
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        foreach ($sourceTableData as $sourceBookTranslation) {
            $this->importBookTranslation($sourceBookTranslation);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    /**
     * @param array $sourceBookTranslation
     * @return BookTranslation
     * @throws \Doctrine\DBAL\DBALException
     */
    private function importBookTranslation(array $sourceBookTranslation)
    {
        if ($this->getMappedBook($sourceBookTranslation)) {
            $query = "INSERT INTO ".$this->targetDb.".book_translation VALUES (
                    NULL, 
                    '".str_replace("'", "''", $this->getMappedLang($sourceBookTranslation)['id'])."', 
                    '".str_replace("'", "''", $this->getMappedBook($sourceBookTranslation)['id'])."', 
                    '".str_replace("'", "''", $sourceBookTranslation['title'])."'
                    )";
            $this->connection->prepare($query)->execute();
        }

        return $this;
    }

    /**
     * @param array $sourceBookTranslation
     * @return Book|object|null
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getMappedBook(array $sourceBookTranslation)
    {
        $newId = $this->getNewIdFromTmpTableMap('book', $sourceBookTranslation['book_id']);

        if (!is_bool($newId)) {
            $query = 'SELECT id FROM ' . $this->targetDb . '.book WHERE id=' . $newId['new_id'] . ';';

            return  $this->connection->query($query)->fetch();
        }

        return null;
    }
}
