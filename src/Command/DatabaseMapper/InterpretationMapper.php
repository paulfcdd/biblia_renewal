<?php


namespace App\Command\DatabaseMapper;


use App\Entity\Chapter;
use App\Entity\ChapterInterpretation;
use App\Entity\Interpretation;
use Symfony\Component\Console\Helper\ProgressBar;

class InterpretationMapper extends AbstractMapper
{
    public function run()
    {
        $interpretationResourcesSourceTableData = $this->getSourceTableData('interpretation_resources');
        $this->output->writeln('<info>ИМПОРТ ТАБЛИЦЫ ИНТЕРПРЕТАЦИЙ</info>');

        $mappedChapters = $this->getMappedChapters();
        $progressBar = new ProgressBar($this->output, count($mappedChapters));
        foreach ($mappedChapters as $mappedChapter) {
            $dataToImport = $this->getDataToImport($mappedChapter);
            $this->importInterpretation($dataToImport);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    private function getMappedChapters()
    {
        $tableName = $this->targetDb . '.' . self::TEMP_TABLE_PREFIX . 'chapter';
        $query = "SELECT * FROM $tableName";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function getDataToImport(array $mappedChapter)
    {
        $chapterOldId = $mappedChapter['old_id'];
        $chapterNewId = $mappedChapter['new_id'];
        $query = "select distinct
                    resources.title, resources.base, interpretations.interpretation_id old_chaper, links.path
                    from
                         ".$this->sourceDb.".chapters odb_chapters
                    left join ".$this->sourceDb.".interpretations interpretations
                        on odb_chapters.id=interpretations.interpretation_id
                    left join ".$this->sourceDb.".interpretation_links links
                        on interpretations.interpretation_link_id = links.id
                    left join ".$this->sourceDb.".interpretation_resources resources
                        on links.resource_id = resources.id
                    where odb_chapters.id = $chapterOldId;
                    ";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $results['data'] = $stmt->fetchAll();
        $results['new_chapter_id'] = $chapterNewId;

        return $results;

    }

    private function importInterpretation(array $dataToImport)
    {

        $query = "SELECT id FROM $this->targetDb.chapter WHERE id=" . $dataToImport['new_chapter_id'];
        $stmt = $this->connection->query($query);
        $chapter = $stmt->fetch();

        if ($chapter) {
            foreach ($dataToImport['data'] as $dataSet) {

                $query = "INSERT INTO ".$this->targetDb.".interpretation VALUES (
                    NULL, 
                    '".str_replace("'", "''", !is_null($dataSet['title']) ? $dataSet['title'] : 'No title')."', 
                    '".str_replace("'", "''", !is_null($dataSet['base']) ? $dataSet['base'] : 'No url')."' 
                    )";

                $this->connection->prepare($query)->execute();

                $this->relateChapterInterpretation($chapter['id'], $this->connection->lastInsertId(), $dataSet);
            }
        }

        return $this;
    }

    /**
     * @param int $chapter
     * @param Interpretation $interpretation
     * @param array $dataSet
     * @return ChapterInterpretation
     */
    private function relateChapterInterpretation(int $chapter, int $interpretation, array $dataSet)
    {
        $query = "INSERT INTO ".$this->targetDb.".chapter_interpretation VALUES (
                    NULL, 
                    '".str_replace("'", "''", $interpretation)."', 
                    '".str_replace("'", "''", $chapter)."', 
                    '".str_replace("'", "''", !is_null($dataSet['path']) ? $dataSet['path'] : 'No url')."' 
                    )";

        $this->connection->prepare($query)->execute();
        return $this;
    }
}
