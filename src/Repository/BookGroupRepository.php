<?php

namespace App\Repository;

use App\Entity\BookGroup;
use App\Entity\BookGroupCode;
use App\Entity\BookGroupTranslation;
use App\Entity\Lang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method BookGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookGroup[]    findAll()
 * @method BookGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookGroup::class);
    }

    /**
     * @param Lang $lang
     * @param int $testamentType
     * @return mixed
     */
    public function getBookGroupsByTestamentWithTranslations(Lang $lang, int $testamentType)
    {

        $query = $this->createQueryBuilder('book_group')
            ->select('book_group.title, book_group.id as bookGroupId, lang.id as langId, bgc.code')
            ->leftJoin(BookGroupTranslation::class, 'book_group_translation', Expr\Join::WITH, 'book_group.id = book_group_translation.bookGroup')
            ->leftJoin(Lang::class, 'lang', Expr\Join::WITH, 'book_group_translation.lang = lang.id')
            ->leftJoin(BookGroupCode::class, 'bgc', Expr\Join::WITH, 'bgc.bookGroup = book_group.id')
            ->where('book_group.section = :testamentType')
            ->andWhere('lang.id = :lang')
            ->setParameters([
                'testamentType' => $testamentType,
                'lang' => $lang->getId(),
            ])
            ->getQuery();

        return $query->getResult();
    }
}
