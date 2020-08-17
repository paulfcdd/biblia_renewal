<?php


namespace App\Command\DatabaseMapper;


use Symfony\Component\Console\Helper\ProgressBar;

class MainPageLabelsMapper extends AbstractMapper
{
    public function run()
    {
        $sourceTableData = $this->getSourceTableData('main_page_labels');
        $this->output->writeln('<info>ИМПОРТ ТАБЛИЦЫ ЛЕЙБЛОВ ГЛАВНОЙ СТРАНИЦЫ</info>');
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        foreach ($sourceTableData as $sourcePageLabel) {
            if ($this->getMappedLang($sourcePageLabel)) {
                $query = "INSERT INTO ".$this->targetDb.".main_page_labels VALUES (
                    NULL,
                    '".str_replace("'", "''", $this->getMappedLang($sourcePageLabel)['id'])."',
                    '".str_replace("'", "''", $sourcePageLabel['code'])."',
                    '".str_replace("'", "''", $sourcePageLabel['title'])."'
                    )";
                $this->connection->prepare($query)->execute();
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return $this;
    }
}
