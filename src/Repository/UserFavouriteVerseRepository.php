<?php

namespace App\Repository;

use App\Entity\UserFavouriteVerse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserFavouriteVerse|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFavouriteVerse|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFavouriteVerse[]    findAll()
 * @method UserFavouriteVerse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFavouriteVerseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFavouriteVerse::class);
    }
}
