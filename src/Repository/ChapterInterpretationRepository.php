<?php

namespace App\Repository;

use App\Entity\ChapterInterpretation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChapterInterpretation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChapterInterpretation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChapterInterpretation[]    findAll()
 * @method ChapterInterpretation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChapterInterpretationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChapterInterpretation::class);
    }

}
