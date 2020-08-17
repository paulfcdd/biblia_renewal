<?php

namespace App\Repository;

use App\Entity\Lang;
use App\Entity\MainPageLabels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MainPageLabels|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainPageLabels|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainPageLabels[]    findAll()
 * @method MainPageLabels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainPageLabelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainPageLabels::class);
    }

    /**
     * @param Lang $lang
     * @return mixed
     */
    public function getLabelsByLang(Lang $lang)
    {
        $query = $this->createQueryBuilder('main_page_labels')
            ->select('main_page_labels.code', 'main_page_labels.title')
            ->where('main_page_labels.lang = :langId')
            ->setParameter('langId', $lang->getId())
            ->getQuery()
        ;

        $result = $query->getResult();
        $preparedResult = [];
        foreach ($result as $item) {
            $preparedResult[$item['code']] = $item['title'];
        }

        return $preparedResult;
    }

    public function getTranslationByLangId(int $langId)
    {
        $query = $this->createQueryBuilder('main_page_labels')
            ->where('main_page_labels.lang = :langId')
            ->setParameter('langId', $langId)
            ->getQuery()
        ;

        return $query->getResult();
    }
}
