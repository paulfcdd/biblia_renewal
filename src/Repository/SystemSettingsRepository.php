<?php

namespace App\Repository;

use App\Entity\SystemSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemSettings[]    findAll()
 * @method SystemSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemSettings::class);
    }
}
