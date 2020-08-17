<?php


namespace App\Command\DatabaseMapper;


use App\Entity\Lang;
use Symfony\Component\Console\Helper\ProgressBar;

class LangMapper extends AbstractMapper
{
    /**
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run()
    {
        $sourceTableData = $this->getSourceTableData('langs');

        $this->output->writeln('<info>ИМПОРТ ТАБЛИЦЫ ЯЗЫКОВ</info>');
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        foreach ($sourceTableData as $singleItem) {
            $this->importLang($singleItem);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    /**
     * @param array $sourceLang
     * @return Lang
     * @throws \Doctrine\DBAL\DBALException
     */
    private function importLang(array $sourceLang)
    {

        $query = "INSERT INTO ".$this->targetDb.".lang VALUES (
                    NULL, 
                    '".str_replace("'", "''", $sourceLang['title'])."', 
                    '".str_replace("'", "''", $sourceLang['menu_title'])."', 
                    '".$sourceLang['code']."', 
                    '".$sourceLang['code']."', 
                    '".$sourceLang['ordering']."', true, false 
                    )";
        $this->connection->prepare($query)->execute();

//        $lang = new Lang();
//        $lang
//            ->setTitle($sourceLang['title'])
//            ->setNativeTitle($sourceLang['menu_title'])
//            ->setUrlSlugCode($sourceLang['code'])
//            ->setIsoCode($sourceLang['code'])
//            ->setIsActive(false)
//            ->setIsBlocked(true)
//            ->setSortOrder($sourceLang['ordering']);
//
//        $this->entityManager->persist($lang);
//        $this->entityManager->flush();

        $this->fillTmpTableMap('lang', $sourceLang['id'], $this->connection->lastInsertId());

        return $this;
    }
}
