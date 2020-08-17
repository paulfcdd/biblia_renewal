<?php


namespace App\Command\DatabaseMapper;


use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\ChapterTranslation;
use App\Entity\Lang;
use Symfony\Component\Console\Helper\ProgressBar;

class ChapterMapper extends AbstractMapper
{
    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run()
    {
        $sourceTableData = $this->getSourceTableData('chapters');

        $this->output->writeln('<info>ИМПОРТ ТАБЛИЦЫ ГЛАВ</info>');
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        foreach ($sourceTableData as $sourceChapter) {
            $chapter = $this->importChapter($sourceChapter);
            if ($chapter) {
                $this->importChapterTranslation($chapter, $sourceChapter);
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    /**
     * @param array $sourceChapter
     * @return string|null
     * @throws \Doctrine\DBAL\DBALException
     */
    private function importChapter(array $sourceChapter)
    {
        if ($this->getMappedBook($sourceChapter)) {
            $query = "INSERT INTO ".$this->targetDb.".chapter VALUES (
                    NULL, 
                    '".str_replace("'", "''", $this->getMappedBook($sourceChapter)['id'])."', 
                    '".str_replace("'", "''", $sourceChapter['number'])."' 
                    )";
            $this->connection->prepare($query)->execute();
            $this->fillTmpTableMap('chapter', $sourceChapter['id'], $this->connection->lastInsertId());

            return $this->connection->lastInsertId();
        }

        return null;
    }

    /**
     * @param Chapter $chapter
     * @param array $sourceChapter
     * @return ChapterTranslation
     * @throws \Doctrine\DBAL\DBALException
     */
    private function importChapterTranslation(int $chapter, array $sourceChapter)
    {
//        $chapterTranslation = new ChapterTranslation();
//
//        $chapterTranslation
//            ->setChapter($chapter)
//            ->setLang($this->getMappedLang($sourceChapter));
//
//        $this->entityManager->persist($chapterTranslation);
//        $this->entityManager->flush();

        $query = "INSERT INTO ".$this->targetDb.".chapter_translation VALUES (
                    NULL, 
                    '".str_replace("'", "''", $chapter)."', 
                    '".str_replace("'", "''", $this->getMappedLang($sourceChapter)['id'])."' 
                    )";
        $this->connection->prepare($query)->execute();

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
