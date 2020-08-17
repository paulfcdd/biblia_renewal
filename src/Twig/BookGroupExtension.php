<?php


namespace App\Twig;


use App\Entity\BookGroup;
use Twig\TwigFilter;

class BookGroupExtension extends AppAbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('book_group_title', [$this, 'getBookGroupTitle'])
        ];
    }


    public function getBookGroupTitle(int $bookGroupId)
    {
        /** @var BookGroup $bookGroup */
        $bookGroup = $this->entityManager->getRepository(BookGroup::class)->findOneBy(['id' => $bookGroupId]);

//        dd($bookGroup);
        return $bookGroup->getTitle();
    }
}
