<?php

namespace App\Repository;

use App\Entity\BookGroupTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookGroupTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookGroupTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookGroupTranslation[]    findAll()
 * @method BookGroupTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookGroupTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookGroupTranslation::class);
    }
}
