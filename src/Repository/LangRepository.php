<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookTranslation;
use App\Entity\Lang;
use App\Entity\SystemSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lang|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lang|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lang[]    findAll()
 * @method Lang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LangRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lang::class);
    }

    /**
     * @return mixed
     */
    public function getIdCodeTitle()
    {
        $query = $this->createQueryBuilder('lang')
            ->select('lang.id', 'lang.nativeTitle', 'lang.urlSlugCode')
            ->orderBy('lang.sortOrder', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return mixed
     */
    public function getAvailableLangs()
    {
        $query = $this->createQueryBuilder('lang')
            ->where('lang.isActive = :active')
            ->andWhere('lang.isBlocked = :blocked')
            ->orderBy('lang.sortOrder', 'asc')
            ->setParameter('active', true)
            ->setParameter('blocked', false)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $code
     * @param bool $isAvailable
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLangIdByCode(string $code, bool $isAvailable = true)
    {
        $query = $this->createQueryBuilder('lang')
            ->where('lang.urlSlugCode = :code')
            ->orWhere('lang.isoCode = :code')
            ->setParameter('code', $code)
        ;

        if ($isAvailable) {
            $query
                ->andWhere('lang.isActive = :active')
                ->andWhere('lang.isBlocked = :blocked')
                ->setParameter('active', true)
                ->setParameter('blocked', false)
            ;
        }

        $query = $query->getQuery();

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function getLangIdByIsoCode(string $isoCode, bool $isAvailable = true)
    {
        $query = $this->createQueryBuilder('lang')
            ->where('lang.isoCode = :code')
            ->setParameter('code', $isoCode)
        ;

        if ($isAvailable) {
            $query
                ->andWhere('lang.isActive = :active')
                ->andWhere('lang.isBlocked = :blocked')
                ->setParameter('active', true)
                ->setParameter('blocked', false)
            ;
        }

        $query = $query->getQuery();

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * @return mixed
     */
    public function selectMaxOrdering()
    {
        $query = $this->createQueryBuilder('lang')
            ->select('MAX(lang.ordering) AS max_ordering')
            ->orderBy('max_ordering')
            ->getQuery();

        return $query->getResult();
    }

    public function getBookTranslationLangs(Book $book)
    {
        $query = $this->createQueryBuilder('lang')
            ->leftJoin(BookTranslation::class, 'bt', 'WITH', 'lang.id = bt.lang')
            ->leftJoin(Book::class, 'b', 'WITH', 'bt.book = b.id')
            ->where('b.id = :bookId')
            ->setParameter('bookId', $book->getId())
            ->orderBy('lang.sortOrder', 'ASC')
            ->getQuery()
        ;

        return $query->getResult();
    }
}
