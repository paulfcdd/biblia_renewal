<?php

namespace App\Repository;

use App\Entity\VerseTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VerseTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerseTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerseTranslation[]    findAll()
 * @method VerseTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerseTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerseTranslation::class);
    }
}
