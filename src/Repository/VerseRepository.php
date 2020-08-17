<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\Verse;
use App\Entity\VerseTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Verse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verse[]    findAll()
 * @method Verse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Verse::class);
    }

    /**
     * @param int $langId
     * @param int $bookId
     * @param int|null $chapterId
     * @param int|null $limit
     * @param int|null $offset
     * @return mixed
     */
    public function getVersesForBookAndChapter(int $langId, int $bookId, int $chapterId = null, int $limit = null, int $offset = null)
    {

        $query = $this->createQueryBuilder('verse')
            ->select('verse_translation.preparedTranslation as text, verse.number as code')
            ->leftJoin(VerseTranslation::class, 'verse_translation', Expr\Join::WITH, 'verse_translation.verse=verse.id')
            ->leftJoin(Chapter::class, 'chapter', Expr\Join::WITH, 'chapter.id=verse.chapter')
            ->leftJoin(Book::class, 'book', Expr\Join::WITH, 'book.id=chapter.book')
            ->where('verse_translation.lang = :lang')
            ->andWhere('chapter.id = :chapter')
            ->andWhere('book.id = :book')
            ->setParameters([
                'lang' => $langId,
                'chapter' => $chapterId,
                'book' => $bookId,
            ])
            // TODO: rewrite using CAST method
            ->orderBy('verse.slug+0', 'ASC')
        ;

        if ($offset) {
            $query->setFirstResult($offset);
        }

        if ($limit) {
            $query->setMaxResults($limit);
        }

        if ($chapterId) {
            $query->andWhere('verse.chapter = :chapter')->setParameter('chapter', $chapterId);
        }

        return $query->getQuery()->getResult();
    }

    public function getChapterRangeVerses(int $langId, int $bookId, int $chapterFrom, int $chapterTo)
    {
        $query = $this->createQueryBuilder('verse')
            ->select('verse_translation.preparedTranslation as text, verse.number as code, chapter.number as chapter_number, verse.number as verse_number')
            ->leftJoin(VerseTranslation::class, 'verse_translation', Expr\Join::WITH, 'verse_translation.verse=verse.id')
            ->leftJoin(Chapter::class, 'chapter', Expr\Join::WITH, 'chapter.id=verse.chapter')
            ->leftJoin(Book::class, 'book', Expr\Join::WITH, 'book.id=chapter.book')
            ->where('verse_translation.lang = :lang')
            ->andWhere('chapter.id BETWEEN :chapterFrom AND :chapterTo')
            ->andWhere('book.id = :book')
            ->setParameters([
                'lang' => $langId,
                'chapterFrom' => $chapterFrom,
                'chapterTo' => $chapterTo,
                'book' => $bookId,
            ])
//            // TODO: rewrite using CAST method
            ->orderBy('verse.slug+0', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
