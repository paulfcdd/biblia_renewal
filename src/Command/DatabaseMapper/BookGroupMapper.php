<?php


namespace App\Command\DatabaseMapper;


use App\Entity\BookGroup;
use App\Entity\BookGroupCode;
use App\Entity\Section;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Console\Helper\ProgressBar;

class BookGroupMapper extends AbstractMapper
{
    public const BOOK_GROUP_TESTAMENT_MAP = [
        'old_testament' => [
            'Пятикнижие',
            'Исторические книги',
            'Учительные книги',
            'Пророческие книги',
        ],
        'new_testament' => [
            'Евангелия',
            'Соборные послания',
            'Послания ап. Павла'
        ],
    ];

    private const BOOK_GROUP_CODES = [
        'moses5Books' => 'Пятикнижие',
        'historical' => 'Исторические книги',
        'teachBooks' => 'Учительные книги',
        'prophetsBooks' => 'Пророческие книги',
        'gospels' => 'Евангелия',
        'cathedralEpistles' => 'Соборные послания',
        'epistlesOfPavel' => 'Послания ап. Павла',
//        'propheticBook' => 'Книга пророческая'
    ];

    /**
     * @return int
     * @throws DBALException
     */
    public function run()
    {
        $sourceTableData = $this->getSourceTableData('book_groups');
        $bookGroupMap = self::BOOK_GROUP_TESTAMENT_MAP;
        $bookGroupCodes = array_flip(self::BOOK_GROUP_CODES);

        $this->output->writeln('<info>ИМПОРТ ГРУПП КНИГ</info>');
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        foreach ($sourceTableData as $singleItem) {
            array_walk($bookGroupMap, function (&$item, &$key) use ($singleItem, $bookGroupCodes) {
                if (in_array($singleItem['title'], $item)) {
                    /** @var Section $section */
                    $section = $this->getSectionByCode($key);

                    $query = "INSERT INTO ".$this->targetDb.".book_group VALUES (
                    NULL, 
                    '".str_replace("'", "''", $section->getId())."' ,
                    '".str_replace("'", "''", $singleItem['title'])."' 
                    )";
                    $this->connection->prepare($query)->execute();

                    $this->fillTmpTableMap('book_group', $singleItem['id'], $this->connection->lastInsertId());
                    $this->relateBookGroupWithCode($this->connection->lastInsertId(), $bookGroupCodes[$singleItem['title']]);
                }
            });
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');

        return 0;
    }

    /**
     * @param int $bookGroupId
     * @param string $code
     * @return BookGroupCode
     */
    private function relateBookGroupWithCode(int $bookGroupId, string $code)
    {
        $query = "INSERT INTO ".$this->targetDb.".book_group_code VALUES (
                    NULL, 
                    '".str_replace("'", "''", $bookGroupId)."', 
                    '".str_replace("'", "''", $code)."'
                    )";
        $this->connection->prepare($query)->execute();

//        $bookGroupCode = new BookGroupCode();
//        $bookGroupCode
//            ->setBookGroup($bookGroup)
//            ->setCode($code);
//        $this->entityManager->persist($bookGroupCode);
//        $this->entityManager->flush();

        return $this;
    }

    /**
     * @param string $code
     * @return Section|null|object
     */
    private function getSectionByCode(string $code)
    {
        return $this->entityManager->getRepository(Section::class)->findOneBy([
            'code' => $code
        ]);
    }


}
