<?php

namespace App\Repository;

use App\Entity\VerseOriginNumbering;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VerseOriginNumbering|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerseOriginNumbering|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerseOriginNumbering[]    findAll()
 * @method VerseOriginNumbering[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerseOriginNumberingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerseOriginNumbering::class);
    }
}
