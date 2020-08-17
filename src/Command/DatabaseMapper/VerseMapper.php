<?php


namespace App\Command\DatabaseMapper;


use App\Entity\Chapter;
use App\Entity\Lang;
use App\Entity\Verse;
use App\Entity\VerseTranslation;
use Symfony\Component\Console\Helper\ProgressBar;

class VerseMapper extends AbstractMapper
{
    public function run()
    {
        $sourceTableData = $this->getSourceTableData('verses');
        $this->output->writeln('<info>ИМПОРТ ТАБЛИЦЫ СТИХОВ</info>');
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        foreach ($sourceTableData as $sourceVerse) {
            $verse = $this->importVerse($sourceVerse);
            if ($verse) {
                $this->importVerseTranslation($verse, $sourceVerse);
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    /**
     * @param array $sourceVerse
     * @return string|null
     * @throws \Doctrine\DBAL\DBALException
     */
    private function importVerse(array $sourceVerse)
    {
        if ($this->getMappedChapter($sourceVerse)) {
            $query = "INSERT INTO ".$this->targetDb.".verse VALUES (
                    NULL, 
                    '".str_replace("'", "''", $this->getMappedChapter($sourceVerse))."', 
                    '".str_replace("'", "''", $sourceVerse['code'])."',
                    '".str_replace("'", "''", $sourceVerse['uverse'])."' 
                    )";
            $this->connection->prepare($query)->execute();

            $this->fillTmpTableMap('verse', $sourceVerse['id'], $this->connection->lastInsertId());

            return $this->connection->lastInsertId();
        }

        return null;
    }

    /**
     * @param Verse $importedVerse
     * @param array $sourceVerse
     * @return VerseTranslation
     */
    private function importVerseTranslation(int $importedVerse, array $sourceVerse)
    {
//        $verseTranslation = new VerseTranslation();
//        $verseTranslation
//            ->setVerse($importedVerse)
//            ->setLang($this->getMappedLang($sourceVerse))
//            ->setOriginalTranslation($sourceVerse['text'])
//            ->setPreparedTranslation($sourceVerse['text_clear']);
//
//        $this->entityManager->persist($verseTranslation);
//        $this->entityManager->flush();


        $query = "INSERT INTO ".$this->targetDb.".verse_translation VALUES (
                    NULL, 
                    '".str_replace("'", "''", $importedVerse)."', 
                    '".str_replace("'", "''", $this->getMappedLang($sourceVerse)['id'])."',
                    '".str_replace("'", "''", $sourceVerse['text'])."',
                    '".str_replace("'", "''", $sourceVerse['text_clear'])."'
                    )";
        $this->connection->prepare($query)->execute();

        return $this;

    }

    /**
     * @param array $sourceVerse
     * @return Chapter|object|null
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getMappedChapter(array $sourceVerse)
    {
        $newChapter = $this->getNewIdFromTmpTableMap('chapter', $sourceVerse['chapter_id']);

        if (!is_bool($newChapter)) {
            $query = 'SELECT id FROM ' . $this->targetDb . '.chapter WHERE id=' . $newChapter['new_id'] . ';';

            return  $this->connection->query($query)->fetch()['id'];
        }

        return null;
    }
}
