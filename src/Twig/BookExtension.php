<?php


namespace App\Twig;


use App\Entity\Book;
use App\Entity\BookGroup;
use App\Entity\Lang;
use Twig\TwigFunction;

class BookExtension extends AppAbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('render_book_titles_by_testament_and_lang', [$this, 'renderBookTitlesByTestamentAndLang']),
            new TwigFunction('get_book_titles_by_lang_and_book_group', [$this, 'getBookTitlesByLangAndBookGroup']),
            new TwigFunction('get_book_titles_by_lang_and_url_title', [$this, 'getBookTitleByLangCodeAndUrlTitle']),
            new TwigFunction('get_book_by_lang', [$this, 'getBooksByLang']),
            new TwigFunction('generate_book_link', [$this, 'generateBookLink']),

        ];
    }

    public function generateBookLink(string $p1,  string $p2, string $p3, string $p4) {
        // todo: this is stub
        return 'https://azbyka.ru/biblia/';
    }

    /**
     * @param int $testamentType
     * @param int $langId
     * @param string $requestLocale
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderBookTitlesByTestamentAndLang(int $testamentType, int $langId, string $requestLocale)
    {
        // TODO: Сделать рефакторинг метода
        /** @var Lang $lang */
        $lang = $this->entityManager->getRepository(Lang::class)->findOneBy(['id' => $langId]);
        $booksGroupsByTestament = $this->entityManager->getRepository(BookGroup::class)->getBookGroupsByTestamentWithTranslations($lang, $testamentType);

        return $this->twigEnvironment->render('front/partials/book/render_books_by_testament.html.twig', [
            'books_groups_by_testament' => $booksGroupsByTestament,
            'testament_type' => $testamentType,
            'request_locale' => $requestLocale,
        ]);
    }

    /**
     * @param int $langId
     * @param int $bookGroupId
     * @return mixed
     */
    public function getBookTitlesByLangAndBookGroup(int $langId, int $bookGroupId)
    {
        $results = $this->entityManager->getRepository(Book::class)
            ->getBooksByLangAndGroup($langId, $bookGroupId);

        switch ($bookGroupId) {
            case 2:
                $preparedResults = [];

                foreach ($results as $result) {
                    if (strpos($result['abbreviation'], 'Цар') !== false) {
                        $preparedResults['Цар'][] = $result;
                    } elseif (strpos($result['abbreviation'], 'Пар') !== false) {
                        $preparedResults['Пар'][] = $result;
                    } elseif (strpos($result['abbreviation'], 'Ездр') !== false) {
                        $preparedResults['Ездр'][] = $result;
                    } elseif (strpos($result['abbreviation'], 'Мак') !== false) {
                        $preparedResults['Мак'][] = $result;
                    } else {
                        $preparedResults[] = $result;
                    }
                }
                
                return $preparedResults;
            case 4:
                $preparedResults = [];

                foreach ($results as $result) {

                    if (in_array($result['slug'], ['Lam', 'pJer'])) {
                        $preparedResults['jeremiah'][] = $result;
                    } else {
                        $preparedResults['books'][] = $result;
                    }
                }

                return $preparedResults;
        }


        return $results;
    }

    /**
     * @param string $langCode
     * @param string $slug
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBookTitleByLangCodeAndUrlTitle(string $langCode, string $urlTitle)
    {
        return $this->entityManager->getRepository(Book::class)
            ->getBookTitleByLangCodeAndUrlTitle($urlTitle, $langCode);
    }

    /**
     * @param int $lang
     * @param string $locale
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getBooksByLang(int $lang, string $locale)
    {
        $books = $this->entityManager->getRepository(Book::class)->getBooksByLang($lang);

        return $this->twigEnvironment->render('front/partials/common/books_by_lang_tab.html.twig', [
            'books' => $books,
            'request_locale' => $locale
        ]);
    }
}
