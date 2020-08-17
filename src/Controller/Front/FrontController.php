<?php


namespace App\Controller\Front;


use App\Controller\AppController;
use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\Lang;
use App\Entity\MainPageLabels;
use App\Entity\Section;
use App\Entity\Verse;
use Symfony\Component\HttpFoundation\Response;

class FrontController extends AppController
{
    /** @var Lang */
    public $requestLang;
    /**
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function indexPage()
    {
        $this->initialize();

        if (!empty($this->queryParameters)) {
            if ($this->checkIsLocale($this->queryParameters[0])) {
                $this->requestedLocale = $this->queryParameters[0];
            } else {
                $bookAndPageArray = explode('_', $this->queryParameters[0]);
                $this->requestedLocale = $this->queryParameters[1];

                if ($this->isMultipleLangs()) {
                    $requestedLangs = explode('~', $this->requestedLocale);

                    return $this->multipleLangsBookContentPage($requestedLangs, $bookAndPageArray[0], $bookAndPageArray[1]);

                } else {
                    $urlTitle = $bookAndPageArray[0];
                    $chapterNumber = $bookAndPageArray[1];

                    if (intval(strpos($chapterNumber, '-'))) {
                        $chapterRange = explode('-', $chapterNumber);

                        return $this->multipleChapterBookContentPage($urlTitle, $chapterRange);
                    } else {
                        $chapterNumber = intval($bookAndPageArray[1]);

                        return $this->bookContentPage($urlTitle, $chapterNumber);
                    }
                }
            }
        } else {
            $this->requestedLocale = $this->systemDefaultLanguageCode;
        }

        $this->setRequestLang();

        /** @var MainPageLabels $mainPageLabels */
        $mainPageLabels = $this->entityManager->getRepository(MainPageLabels::class)->getLabelsByLang($this->requestLang);
        /** @var array $langs */
        $langs = $this->entityManager->getRepository(Lang::class)->getAvailableLangs();
        /** @var Section $oldTestament */
        $oldTestament = $this->entityManager->getRepository(Section::class)->findOneBy(['code' => 'old_testament']);
        /** @var Section $newTestament */
        $newTestament = $this->entityManager->getRepository(Section::class)->findOneBy(['code' => 'new_testament']);

        return $this->render('front/pages/index.html.twig', array_merge([
            'oldTestamentType' => $oldTestament->getId(),
            'newTestamentType' => $newTestament->getId(),
            'langId' => $this->requestLang->getId(),
            'langs' => $langs,
            'title' => $this->translator->trans('main_page_title'),
            'oldTestamentBooksGroup' => $oldTestament->getBookGroups(),
            'newTestamentBooksGroup' => $newTestament->getBookGroups(),
            'request_locale' => $this->requestedLocale,
        ], $mainPageLabels));
    }

    /**
     * @param string $_locale
     * @param string $urlTitle
     * @param int $chapterNumber
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function bookContentPage(string $urlTitle, int $chapterNumber)
    {
        /** @var Book $book */
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['slug' => $urlTitle]);
        /** @var Lang $lang */
        $lang = $this->getLangByCode($this->requestedLocale);
        /** @var Chapter $chapter */
        $chapter = $this->getChapter($lang, $book, $chapterNumber);
        $chapters = $this->entityManager->getRepository(Chapter::class)->getChaptersByBookIdAndLangId($lang->getId(), $book->getId());
        $title = $this->entityManager->getRepository(Book::class)->getAbbreviationBySlug($urlTitle) . '. ' . $chapter->getNumber();
        $verses = $this->entityManager->getRepository(Verse::class)->getVersesForBookAndChapter($lang->getId(), $book->getId(), $chapter->getId());

        $chapterCount = count($chapters);
        $currentChapter = $chapter->getNumber();
        $firstPage = 1;
        $previousPage = $currentChapter > $firstPage ? $currentChapter - 1 : null;
        $nextPage = $currentChapter >= $firstPage ? $currentChapter + 1 : null;

        return $this->render('front/pages/book_content.html.twig', [
            'lang' => $lang,
            'book' => $book,
            'title' => $title,
            'urlTitle' => $urlTitle,
            'locale' => $this->requestedLocale,
            'chaptersCount' => $chapterCount,
            'currentChapter' => $currentChapter,
            'verses' => $verses,
            'chapter' => $chapter,
            'firstPage' => $firstPage,
            'previousPage' => $previousPage,
            'nextPage' => $nextPage,
        ]);
    }

    /**
     * @param array $langs
     * @param string $urlTitle
     * @param string $chapterNumber
     * @return Response
     */
    public function multipleLangsBookContentPage(array $langs, string $urlTitle, string $chapterNumber)
    {
        $chapterRange = null;

        if (preg_match('/\d+[\-]\d+/', $chapterNumber)) {
            $chapterRange = explode('-', $chapterNumber);
        }

        /** @var Book $book */
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['slug' => $urlTitle]);
        /** @var Lang $defaultLang */
        $defaultLang = $this->entityManager->getRepository(Lang::class)->findOneBy(['urlSlugCode' => $this->systemDefaultLanguage->getUrlSlugCode()]);
        $defaultLangChapter = $this->getChapter($defaultLang, $book, $chapterRange ? $chapterRange[0] : $chapterNumber);
        $chapters = $this->entityManager->getRepository(Chapter::class)->getChaptersByBookIdAndLangId($defaultLang->getId(), $book->getId());
        $translations = [];
        $title = $book->getAbbreviation() . '.';

        foreach ($langs as $langCode) {
            /** @var Lang $lang */
            $lang = $this->entityManager->getRepository(Lang::class)->findOneBy(['urlSlugCode' => $langCode]);

            if ($chapterRange) {
                $chapterFromNumber = $chapterRange[0];
                $chapterToNumber = $chapterRange[1];
                /** @var Chapter $chapterFrom */
                $chapterFrom = $this->getChapter($lang, $book, $chapterFromNumber);
                /** @var Chapter $chapterTo */
                $chapterTo = $this->getChapter($lang, $book, $chapterToNumber);
                $verses = $this->entityManager->getRepository(Verse::class)->getChapterRangeVerses($lang->getId(), $book->getId(), $chapterFrom->getId(), $chapterTo->getId());
            } else {
                $chapter = $this->getChapter($lang, $book, $chapterNumber);
                $verses = [];
                if ($chapter instanceof Chapter) {
                    $verses = $this->entityManager->getRepository(Verse::class)->getVersesForBookAndChapter($lang->getId(), $book->getId(), $chapter->getId());
                }
            }

            $translations[$langCode]['verses'] = $verses;
            $translations[$langCode]['lang_title'] = $lang->getTitle();
        }

        $chapterCount = count($chapters);
        $currentChapter = $chapterRange ? $chapterRange[0] : $chapterNumber;
        $firstPage = 1;
        $previousPage = $currentChapter > $firstPage ? $currentChapter - 1 : null;
        $nextPage = $currentChapter >= $firstPage ? $currentChapter + 1 : null;

        if ($chapterRange) {
            $chapterRangeNumbers = [];
            for ($i = $chapterRange[0]; $i<=$chapterRange[1]; $i++) {
                $chapterRangeNumbers[] = $i;
            }
            $title .= implode(',', $chapterRangeNumbers);
        } else {
            $title .= $currentChapter;
        }

        return $this->render('front/pages/book_multiple_content.html.twig', [
            'translations' => $translations,
            'lang' => $defaultLang,
            'book' => $book,
            'chapter' => $defaultLangChapter,
            'urlTitle' => $urlTitle,
            'chaptersCount' => $chapterCount,
            'locale' => $this->systemDefaultLanguageCode,
            'currentChapter' => $chapterNumber,
            'firstPage' => $firstPage,
            'previousPage' => $previousPage,
            'nextPage' => $nextPage,
            'title' => $title,
            'chapterRange' => $chapterRange,
        ]);
    }

    /**
     * @param string $urlTitle
     * @param array $chapterRange
     * @return Response
     */
    private function multipleChapterBookContentPage(string $urlTitle, array $chapterRange)
    {
        /** @var Book $book */
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['slug' => $urlTitle]);
        /** @var Lang $lang */
        $lang = $this->getLangByCode($this->requestedLocale);
        $chapterFromNumber = $chapterRange[0];
        $chapterToNumber = $chapterRange[1];
        $chapters = $this->entityManager->getRepository(Chapter::class)->getChaptersByBookIdAndLangId($lang->getId(), $book->getId());
        /** @var Chapter $chapterFrom */
        $chapterFrom = $this->getChapter($lang, $book, $chapterFromNumber);
        /** @var Chapter $chapterTo */
        $chapterTo = $this->getChapter($lang, $book, $chapterToNumber);
        $verses = $this->entityManager->getRepository(Verse::class)->getChapterRangeVerses($lang->getId(), $book->getId(), $chapterFrom->getId(), $chapterTo->getId());
        $title = $this->entityManager->getRepository(Book::class)->getAbbreviationBySlug($urlTitle) . '.';

        for ($i = $chapterRange[0]; $i<=$chapterRange[1]; $i++) {
            $chapterRangeNumbers[] = $i;
        }
        $title .= implode(',', $chapterRangeNumbers);

        $chapterCount = count($chapters);
        $currentChapter = $chapterFromNumber;
        $firstPage = 1;
        $previousPage = $currentChapter > $firstPage ? $currentChapter - 1 : null;
        $nextPage = $currentChapter >= $firstPage ? $currentChapter + 1 : null;

        return $this->render('front/pages/book_content.html.twig', [
            'lang' => $lang,
            'book' => $book,
            'title' => $title,
            'urlTitle' => $urlTitle,
            'locale' => $this->requestedLocale,
            'chaptersCount' => $chapterCount,
            'currentChapter' => $currentChapter,
            'verses' => $verses,
            'chapter' => $chapterFrom,
            'firstPage' => $firstPage,
            'previousPage' => $previousPage,
            'nextPage' => $nextPage,
            'chapterRange' => $chapterRange,
        ]);
    }

    /**
     * @return $this
     */
    private function setRequestLang()
    {
        if ($this->requestedLocale == $this->siteLocale) {
            $langByCode = $this->entityManager->getRepository(Lang::class)->getLangIdByCode($this->requestedLocale, false);
            $this->requestLang = $langByCode
                ? $langByCode
                : $this->requestLang = $this->entityManager->getRepository(Lang::class)->getLangIdByIsoCode($this->requestedLocale)
            ;
        } else {
            $langByCode = $this->entityManager->getRepository(Lang::class)->getLangIdByCode($this->siteLocale);
            $this->requestLang = $langByCode
                ? $langByCode
                : $this->requestLang = $this->entityManager->getRepository(Lang::class)->getLangIdByIsoCode($this->requestedLocale)
            ;
        }

        return $this;
    }

    /**
     * @param string $subject
     * @return false|int
     */
    private function checkIsLocale(string $subject)
    {
        return preg_match('/^[a-z]{1,2}/', $subject);
    }

    /**
     * @return bool
     */
    private function isMultipleLangs()
    {
        $strpos = strpos($this->requestedLocale,'~');
        if ($strpos and is_int($strpos)) {
            return true;
        }

        return false;
    }

    private function getLangByCode(string $code)
    {
        return $this->entityManager->getRepository(Lang::class)->getLangIdByCode($code);
    }

    /**
     * @param Lang $lang
     * @param Book $book
     * @param $chapterNumber
     * @return mixed
     */
    private function getChapter(Lang  $lang, Book $book, $chapterNumber)
    {
        return $this->entityManager->getRepository(Chapter::class)->getChapterByBookLangAndNumber($lang->getId(), $book->getId(), $chapterNumber);
    }
}
