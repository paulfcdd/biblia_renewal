<?php

namespace App\Repository;

use App\Entity\BookGroupCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookGroupCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookGroupCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookGroupCode[]    findAll()
 * @method BookGroupCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookGroupCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookGroupCode::class);
    }

    // /**
    //  * @return BookGroupCode[] Returns an array of BookGroupCode objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BookGroupCode
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
