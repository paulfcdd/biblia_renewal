<?php


namespace App\Command\DatabaseMapper;


use App\Entity\BookGroup;
use App\Entity\BookGroupCode;
use App\Entity\BookGroupTranslation;
use App\Entity\Lang;
use Symfony\Component\Console\Helper\ProgressBar;

class BookGroupTranslationMapper extends AbstractMapper
{
    /**
     * @return mixed|void
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run()
    {
        $bookGroupTitlesOldDb = $this->getTitlesFromOldTable();
        $this->output->writeln('<info>ИМПОРТ ПЕРЕВОДОВ ГРУПП КНИГ</info>');
        $progressBar = new ProgressBar($this->output, count($bookGroupTitlesOldDb));

        foreach ($bookGroupTitlesOldDb as $oldDbTitle) {
            $lang = $this->getMappedLang($oldDbTitle);
            $bookGroup = $this->getBookGroup($oldDbTitle);
            if ($bookGroup instanceof BookGroup) {

                $query = "INSERT INTO ".$this->targetDb.".book_group_translation VALUES (
                    NULL, 
                    '".str_replace("'", "''", $bookGroup->getId())."', 
                    '".str_replace("'", "''", $lang['id'])."',
                    '".str_replace("'", "''", $oldDbTitle['title'])."'
                    )";

                $this->connection->prepare($query)->execute();

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    /**
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getTitlesFromOldTable()
    {
        $query = 'select * from '.$this->sourceDb.'.main_page_labels where code in(\'moses5Books\', \'historical\', \'teachBooks\', \'prophetsBooks\', \'gospels\', \'cathedralEpistles\', \'epistlesOfPavel\', \'propheticBook\')';
        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param array $oldDbTitle
     * @return BookGroup|object|null
     */
    private function getBookGroup(array $oldDbTitle)
    {
        /** @var BookGroupCode $bookGroupCode */
        $bookGroupCode = $this->entityManager->getRepository(BookGroupCode::class)->findOneBy(['code' => $oldDbTitle['code']]);

        return $bookGroupCode instanceof BookGroupCode ? $bookGroupCode->getBookGroup() : null;
    }
}
