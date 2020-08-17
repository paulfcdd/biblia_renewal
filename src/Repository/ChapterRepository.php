<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\ChapterTranslation;
use App\Entity\Lang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Chapter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chapter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chapter[]    findAll()
 * @method Chapter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chapter::class);
    }

    public function getChaptersByBookIdAndLangId(int $langId, int $bookId)
    {
        $query = $this->createQueryBuilder('chapter')
            ->leftJoin(ChapterTranslation::class, 'chapter_translation', Expr\Join::WITH, 'chapter.id = chapter_translation.chapter')
            ->where('chapter_translation.lang = :langId')
            ->andWhere('chapter.book = :bookId')
            ->setParameters([
                'langId' => $langId,
                'bookId' => $bookId
            ])
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $langId
     * @param int $bookId
     * @param int $number
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getChapterByBookLangAndNumber(int $langId, int $bookId, int $number)
    {
        $query = $this->createQueryBuilder('chapter')
            ->leftJoin(ChapterTranslation::class, 'chapter_translation', Expr\Join::WITH, 'chapter.id = chapter_translation.chapter')
            ->where('chapter_translation.lang = :langId')
            ->andWhere('chapter.book = :bookId')
            ->andWhere('chapter.number = :number')
            ->setParameters([
                'langId' => $langId,
                'bookId' => $bookId,
                'number' => $number
            ])
            ->getQuery();

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function getChapterByBookSlugAndLangCodeAndChapterNumber(string $bookSlug, string $langCode, int $chapterNumber)
    {
        $query = $this->createQueryBuilder('chapter')
            ->leftJoin(ChapterTranslation::class, 'chapter_translation', Expr\Join::WITH, 'chapter.id = chapter_translation.chapter')
            ->leftJoin(Lang::class, 'lang', 'WITH', 'chapter_translation.lang = lang.id')
            ->leftJoin(Book::class, 'book', 'WITH', 'chapter.book = book.id')
            ->where('book.slug = :slug')
            ->andWhere('chapter.number = :number')
            ->andWhere('(lang.isoCode = :code OR lang.urlSlugCode = :code)')
            ->setParameters([
                'slug' => $bookSlug,
                'number' => $chapterNumber,
                'code' => $langCode
            ])
            ->getQuery();

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }
}
