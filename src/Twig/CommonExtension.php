<?php


namespace App\Twig;


use App\Entity\Book;
use App\Entity\Lang;
use App\Entity\MainPageLabels;
use Twig\TwigFunction;

class CommonExtension extends AppAbstractExtension
{
    public const ENTITY_DIR = 'App\\Entity\\';

    public function getFunctions()
    {
        return [
            new TwigFunction('render_lang_selector', [$this, 'renderLangSelector']),
            new TwigFunction('get_languages_array', [$this, 'getLanguagesArray']),
            new TwigFunction('get_translations_for_language', [$this, 'getTranslationsForLanguage']),
            new TwigFunction('render_lang_table', [$this, 'renderLangTable']),
            new TwigFunction('generate_paginate_book_link', [$this, 'generatePaginateBookLink']),
            new TwigFunction('generate_book_link', [$this, 'generateBookLink']),
            new TwigFunction('get_chapters_from_uri', [$this, 'getChaptersFromUri']),
            new TwigFunction('generate_book_link_with_chapter_range', [$this, 'generateBookLinkWithChapterRange']),
        ];
    }

    /**
     * @param string $uri
     * @return array
     */
    public function getChaptersFromUri(string $uri)
    {
        preg_match_all('!\d+!', $uri, $chapters);

        return array_shift($chapters);
    }

    /**
     * @param string $requestLocale
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderLangSelector(string $requestLocale)
    {
        $langs = $this->entityManager->getRepository(Lang::class)->getAvailableLangs();

        return $this->twigEnvironment->render('front/partials/common/lang_selector.html.twig', [
            'langs' => $langs,
            'request_locale' => $requestLocale,
        ]);
    }

    public function renderLangTable(Book $book)
    {
        $langs = $this->entityManager->getRepository(Lang::class)->getBookTranslationLangs($book);

        return $this->twigEnvironment->render('front/partials/common/lang_table.html.twig', [
            'langs' => $langs,
        ]);
    }

    public function generateBookLink(string $path, string $bookTitle, int $page, string $locale)
    {
        $urlFormat = '%s?%s.%d&%s';

        return sprintf($urlFormat, $path, $bookTitle, $page, $locale);
    }

    /**
     * @param string $requestUri
     * @param int $page
     * @return string|string[]|null
     */
    public function generatePaginateBookLink(string $requestUri, int $page, string $newBookTitle = null)
    {
        if ($newBookTitle) {
            $newBookTitleFormat = '?%s.';
            $newBookTitle = sprintf($newBookTitleFormat, $newBookTitle);
            $requestUri = preg_replace('/[?](.*)[.]/', $newBookTitle, $requestUri);
        }
        $requestUri = preg_replace('/\d+[\-]\d+/', $page, $requestUri);
        $requestUri = preg_replace('/\d+/', $page, $requestUri);

        return $requestUri;
    }

    public function generateBookLinkWithChapterRange(int $chaptersCount, string $requestUri)
    {
        $range = '1-'.$chaptersCount;
        $requestUri = preg_replace('/\d+[\-]\d+/', $range, $requestUri);
        $requestUri = preg_replace('/\d+/', $range, $requestUri);

        return $requestUri;
    }


    /**
     * @return array
     */
    public function getLanguagesArray()
    {
        /** @var array $langs */
        $langs = $this->entityManager->getRepository(Lang::class)->getIdCodeTitle();

        return $langs;
    }

    public function getTranslationsForLanguage(string $entity, string $langCode)
    {
        $langId = $this->entityManager->getRepository(Lang::class)->getLangIdByCode($langCode, false);
        $classFQN = $this->getEntityClassFQN($entity);

        return $this->entityManager->getRepository($classFQN)->getTranslationByLangId($langId);
    }

    /**
     * @param string $entity
     * @return string
     */
    private function getEntityClassFQN(string $entity)
    {
        return self::ENTITY_DIR  . $entity;
    }
}
