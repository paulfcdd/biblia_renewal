<?php

namespace App\Repository;

use App\Entity\PasswordResetHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PasswordResetHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasswordResetHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasswordResetHistory[]    findAll()
 * @method PasswordResetHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordResetHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetHistory::class);
    }
}
