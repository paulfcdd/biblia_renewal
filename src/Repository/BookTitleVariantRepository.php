<?php

namespace App\Repository;

use App\Entity\BookTitleVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookTitleVariant|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookTitleVariant|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookTitleVariant[]    findAll()
 * @method BookTitleVariant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookTitleVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookTitleVariant::class);
    }
}
