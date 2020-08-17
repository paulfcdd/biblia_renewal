<?php

namespace App\Tools;

use App\Entity\Book;
use App\Entity\Lang;
use Doctrine\ORM\EntityManagerInterface;

class BookContentLoader
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $urlTitle
     * @return object|null
     */
    public function getBookByUrlTitle($urlTitle)
    {
        return $this->entityManager->getRepository(Book::class)->findOneBy([
            'slug' => $urlTitle,
        ]);
    }

    /**
     * @param string $langCode
     * @return object|null
     */
    public function getLangByCode(string $langCode)
    {
        return $this->entityManager->getRepository(Lang::class)->findOneBy([
            'urlSlugCode' => $langCode
        ]);
    }
}
