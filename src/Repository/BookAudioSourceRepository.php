<?php

namespace App\Repository;

use App\Entity\BookAudioSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookAudioSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookAudioSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookAudioSource[]    findAll()
 * @method BookAudioSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookAudioSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookAudioSource::class);
    }
}
