<?php


namespace App\Command\DatabaseMapper;


use App\Entity\User;
use App\Entity\VerseCrossReference;
use Symfony\Component\Console\Helper\ProgressBar;

class VerseCrossRefMapper extends AbstractMapper
{
    public function run()
    {
        $this->output->writeln('<info>ИМПОРТ ТАБЛИЦЫ ПЕРЕКРЕСТНЫХ ССЫЛОК</info>');
        $sourceTableData = $this->getSourceTableData('verse_crossrefs');
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        foreach ($sourceTableData as $sourceVerseCrossref) {
            $this->importVerseCrossref($sourceVerseCrossref);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    /**
     * @param array $sourceVerseCrossref
     * @return $this
     * @throws \Doctrine\DBAL\DBALException
     */
    private function importVerseCrossref(array $sourceVerseCrossref)
    {
        $query = "INSERT INTO ".$this->targetDb.".verse_cross_reference VALUES (
                    NULL, 
                    '".str_replace("'", "''", $sourceVerseCrossref['uverse_from'])."', 
                    '".str_replace("'", "''", $sourceVerseCrossref['uverse_to'])."' 
                    )";

        $this->connection->prepare($query)->execute();

        return $this;
    }
}
