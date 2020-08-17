<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookAudioSource;
use App\Entity\BookGroup;
use App\Entity\BookGroupTranslation;
use App\Entity\BookTitleVariant;
use App\Entity\BookTranslation;
use App\Entity\Lang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function getTranslationByLangId(int $langId)
    {
        $query = $this->createQueryBuilder('br')
            ->where('br.langId = :langId')
            ->setParameter('langId', $langId)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $langId
     * @param int $bookGroupId
     * @return mixed
     */
    public function getBooksByLangAndGroup(int $langId, int $bookGroupId)
    {
        $query = $this->createQueryBuilder('book')
            ->select('book_translation.title, book.slug, book.abbreviation, book_audio_source.source, book_title_variant.mainTitle as pageTitle, book.hint')
            ->leftJoin(BookTranslation::class, 'book_translation', Expr\Join::WITH, 'book_translation.book = book.id')
            ->leftJoin(BookGroup::class, 'book_group', Expr\Join::WITH, 'book.bookGroup = book_group.id')
            ->leftJoin(BookAudioSource::class, 'book_audio_source', Expr\Join::WITH, 'book_audio_source.book = book.id')
            ->leftJoin(BookTitleVariant::class, 'book_title_variant', Expr\Join::WITH, 'book_title_variant.book = book.id')
            ->where('book_translation.lang = :langId')
            ->andWhere('book.bookGroup = :bookGroupId')
            ->setParameter('langId', $langId)
            ->setParameter('bookGroupId', $bookGroupId)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $slug
     * @param string $code
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAbbreviationBySlug(string $slug)
    {
        $query = $this->createQueryBuilder('book')
            ->select('book.abbreviation')
            ->where('book.slug = :slug')
            ->setParameters([
                'slug' => $slug,
            ])
            ->getQuery();

        return $query->getSingleResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param string $urlTitle
     * @param string $code
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBookTitleByLangCodeAndUrlTitle(string $urlTitle, string $code)
    {
        $query = $this->createQueryBuilder('b')
            ->select('bt.title')
            ->leftJoin(BookTitle::class, 'bt', Expr\Join::WITH, 'bt.book = b.id')
            ->leftJoin(Lang::class, 'l', Expr\Join::WITH, 'bt.lang = l.id')
            ->where('b.urlTitle = :urlTitle')
            ->andWhere('l.code = :code')
            ->setParameters([
                'urlTitle' => $urlTitle,
                'code' => $code
            ])
            ->getQuery();

        return $query->getSingleResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    public function getBooksByLang(int $lang)
    {
        $query = $this->createQueryBuilder('book')
            ->select('book.abbreviation as bookShortTitle, book.slug as slug, book_group_translation.title as bookGroupTitle, book_group.id as bookGroupId')
            ->leftJoin(BookTranslation::class, 'book_translation', Expr\Join::WITH, 'book.id = book_translation.book')
            ->leftJoin(BookGroup::class, 'book_group', Expr\Join::WITH, 'book.bookGroup = book_group.id')
            ->leftJoin(BookGroupTranslation::class, 'book_group_translation', Expr\Join::WITH, 'book.bookGroup = book_group_translation.bookGroup')
            ->where('book_translation.lang = :langId')
            ->andWhere('book_group_translation.lang = :langId')
            ->setParameters([
                'langId' => $lang,
            ])
            ->getQuery();

        return $query->getResult();
    }

    public function getBookInDefaultLang(string $defaultLangCode)
    {

    }
}
