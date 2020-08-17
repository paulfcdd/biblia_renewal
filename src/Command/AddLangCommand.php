<?php


namespace App\Command;


use App\Entity\Book;
use App\Entity\Chapter;
use App\Entity\Lang;
use App\Entity\VerseOriginNumberings;
use App\Entity\Verse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AddLangCommand extends Command
{
    /** @var EntityManagerInterface   */
    public $entityManager;
    /** @var ParameterBagInterface  */
    public $parameterBag;
    /** @var string  */
    protected static $defaultName = 'app:add-lang';
    /** @var string  */
    private const TEXT_FILE_NAME_PATTERN = '_lang_code.txt';
    /** @var string  */
    private const REPLACE_FILE_NAME_PATTERN = '_lang_code.zmn';
    /** @var string  */
    private const NUMERATION_FILE_NAME_PATTERN = '_lang_code.ren';
    /** @var string  */
    private const DEFAULT_IMPORT_ENCODING = 'UTF-8';

    /** @var string */
    private $langCode;
    /** @var string */
    private $langName;
    /** @var string */
    private $langMenuName;
    /** @var string */
    private $langOriginalName;
    /** @var string | null */
    private $langOrdering = null;
    /** @var string */
    private $langEncoding;
    /** @var string */
    private $pathToTextDir;
    /** @var string */
    private $textFile;
    /** @var string */
    private $replaceFile;
    /** @var string */
    private $numerationFile;
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var Lang */
    private $lang;
    private $langChapters;
    private $langVerses;
    private $langVersesOrderingNumber;
    private $textFilePath;
    private $replaceFilePath;
    private $numerationFilePath;


    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag, string $name = null)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
    }

    //"es" "Эстонский" "Эстонский" "Eestlane" "350" "public/texts" "UTF-8"
    public function configure()
    {
        $this
            ->addArgument('lang_code', InputArgument::REQUIRED, 'Код языка')
            ->addArgument('lang_name', InputArgument::REQUIRED, 'Название языка')
            ->addArgument('lang_menu_name', InputArgument::REQUIRED, 'Название языка в меню')
            ->addArgument('lang_original_name', InputArgument::REQUIRED, 'Самоназвание языка')
            ->addArgument('lang_ordering',InputArgument::OPTIONAL, 'Порядок сортировки')
            ->addArgument('lang_encoding',InputArgument::OPTIONAL, 'Кодировка входных файлов')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->pathToTextDir = $this->parameterBag->get('texts_files_path');
        $this->langCode = $input->getArgument('lang_code');
        $this->langName = $input->getArgument('lang_name');
        $this->langMenuName = $input->getArgument('lang_menu_name');
        $this->langOriginalName = $input->getArgument('lang_original_name');
        $this->langOrdering = $input->getArgument('lang_ordering');
        $this->langEncoding = $input->getArgument('lang_encoding') ? $input->getArgument('lang_encoding') : self::DEFAULT_IMPORT_ENCODING;


        $this->textFile = $this->prepareFileName(self::TEXT_FILE_NAME_PATTERN);
        $this->replaceFile = $this->prepareFileName(self::REPLACE_FILE_NAME_PATTERN);
        $this->numerationFile = $this->prepareFileName(self::NUMERATION_FILE_NAME_PATTERN);

        echo 'Checking files in ' . $this->pathToTextDir . PHP_EOL;

        if (!$this->checkDirAndFilesExists()) {
            return 1;
        }

        $output->writeln('All required files are exists');

        $this
            ->checkOrderingNumber()
            ->checkAndUpdateLangInDB()
            ->getLangContentAndShowInfoMessage()
            ;

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Далее будут удалены старые данные языка из базы. продолжить? [y|n]', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('Импорт отменен');
            return 1;
        }

        $output->writeln('confirm');

        $this->removeExistingLangContent();

        $replaceFilter = $this->loadFilter();
        $progressBar = new ProgressBar($output);
        $books = $this->entityManager->getRepository(Book::class)->findAll();

        $this->processLang($books, $progressBar, $replaceFilter);

        $lines = $this->LoadNumberingFile( );

        foreach( $lines as $from => $to ) {

            $verseOriginNumberings = new VerseOriginNumberings();
            $verseOriginNumberings
                ->setLangId($this->lang->getId())
                ->setFrom($from)
                ->setTo($to)
                ;

            $this->entityManager->persist($verseOriginNumberings);
        }

        $this->entityManager->flush();

        return 0;
    }

    /**
     * Загрузка перенумераций из файла
     */
    private function LoadNumberingFile( ) {
        $replaces = file_get_contents( $this->numerationFilePath );

        if ( $this->langEncoding != 'UTF-8' ) {
            $replaces = iconv( $this->langEncoding, 'UTF-8', $replaces );
        }

        $replaces = preg_split( '/\r\n|\n|\r/', $replaces, NULL, PREG_SPLIT_NO_EMPTY );
        $ret = [ ];

        // сборка строк с разбиением на части
        foreach( $replaces as $i => $v ) {
            $sep = preg_quote( mb_substr( $v, 0, 1, 'UTF-8' ), '/' );

            if ( preg_match( '/'.$sep.'/', $v ) ) {
                $tmp = preg_split( '/'.$sep.'/', $v );

                if ( is_array( $tmp ) && isset( $tmp[ 1 ] ) && ( $tmp[ 1 ] != '' ) ) {
                    $index = preg_replace( array( '/\\\r/', '/\\\n/' ), array( "\r", "\n" ), $tmp[ 1 ] );

                    $ret[ $index ] = $tmp[ 2 ];
                }
            }
        }

        return $ret;
    }

    /**
     * Обработка файла языка
     *
     * @param array $books
     * @param ProgressBar $progress
     * @param array $langFilter
     *
     * @return $this
     */
    private function processLang($books, $progress, $langFilter) {
        $filename = $this->textFilePath;
        $lang = $this->lang;

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        /** @var Serializer $serializer */
        $serializer = new Serializer($normalizers, $encoders);

        if ( !file_exists( $filename ) ) {
            $this->output->writeln( 'файл для языка "'.$lang->getTitle().'"('.$filename.') не найден' );
            return 1;
        }

        $text = file_get_contents( $filename );

        if ( $this->langEncoding != 'UTF-8' ) {
            $text = iconv( $this->langEncoding, 'UTF-8', $text );
        }

        $text = preg_split( '/\r\n|\n|\r/', $text, NULL, PREG_SPLIT_NO_EMPTY );

        $this->output->writeln( '' );
        $this->output->writeln( 'обработка файла языка: "'.$lang->getTitle().'"' );

        $this->output->writeln( 'сбор и очистка пустых строк' );

        $this->output->writeln( '1... строк было: '.count( $text ) );
        foreach( $text as $i => $line ) {
            if ( empty( trim( $line ) ) ) {
                unset( $text[ $i ] );
            }
        }
        $this->output->writeln( '1...     стало: '.count( $text ) );

        foreach( $text as $i => $line ) {
            if ( $i && strpos($line, '[') === false) {
                $j = $i - 1;

                if (isset($text[ $j ])) {
                    $tmp = $text[ $j ];

                    while( !preg_match( '/^\[/', $tmp ) ) {
                        --$j;
                        if (isset( $text[ $j ] ) ) {
                            $tmp = $text[ $j ];
                        }
                    }

                    $text[ $j ] .= ' <br/>'.$line;
                }
            }
        }
        $this->output->writeln( '2...     стало: '.count( $text ) );

        foreach( $text as $i => $line ) {
            if(strpos($line, '[') === false){
                unset( $text[ $i ] );
            }
        }
        $this->output->writeln( '3...     стало: '.count( $text ) );
        $this->output->writeln( 'сбор стихов' );

        /** @var Book $book */
        foreach( $books as $book ) {
            $verses = $chapters = array( );
            $notFound = true;

            $this->output->writeln( 'книга: "'.$book->getTitle().'"' );
            $progress->start( count( $text ) );

            foreach( $text as $i => $line ) {
                $progress->advance( );

                if ( preg_match( '/^\['.preg_quote( $book->getCloudTitle() ).'\./u', $line ) ) {
                    $notFound = false;

                    preg_match( '/^\['.preg_quote( $book->getCloudTitle() ).'\.([^\:]+)\:([^\]]+)\](.*)/u', $line, $matches );
                    if ( empty( $matches ) || !isset( $matches[ 1 ] ) ) {
                        $this->output->writeln( 'не сработало регулярное выражение. книга: "'.$book->getCloudTitle().'" язык "'.$lang->getCode().'". строка '.$i );

                        return false;
                    }

                    $chapterCode = $matches[ 1 ];
                    if ( !is_numeric( $chapterCode ) ) {
                        $this->output->writeln( 'глава с особыми символами "'.$chapterCode.'", книга "'.$book->getCloudTitle().'" язык "'.$lang->getCode().'"' );

                        return false;
                    }

                    $verseCode = $matches[ 2 ]; // осторожно, некоторые стихи помечены символами отличными от цифр
                    $verseText = $matches[ 3 ];

                    if ( !isset( $chapters[ $chapterCode ] ) ) {
                        $chapter = new Chapter();
                        $chapter
                            ->setBook($book)
                            ->setLang($lang)
                            ->setNumber($chapterCode);

                        $this->entityManager->persist($chapter);
                        $chapters[ $chapterCode ] = $chapter;
                    }

                    $this->entityManager->flush();

                    $verses[ ] = array(
                        'book_id'		=> $book->getId(),
                        'lang_id'		=> $lang->getId(),
                        'chapter_id'	=> 0,
                        'code'			=> $verseCode,
                        'text'			=> $verseText,
                        'chapter_number' => $chapterCode
                    );
                }
            }

            $progress->finish( );
            $this->output->writeln( '' );

            if ( $notFound ) {
                $this->output->writeln( 'не найдены стихи для книги "'.$book->getTitle().'"('.$book->getCloudTitle().') на языке "'.$lang->getTitle().'"('.$lang->getCode().'.txt)' );
            } else {
                $this->output->writeln( 'глав: '.count( $chapters ) );
                $this->output->writeln( 'всего стихов: '.count( $verses ) );
                $this->output->writeln( 'применение фильтров' );

                $chapters = $this->entityManager->getRepository(Chapter::class)->findBy(['bookId' => $book->getId()]);

                $chapterNumberToId = array( );
                /** @var Chapter $chapter */
                foreach( $chapters as $chapter ) {
                    $chapterNumberToId[ $chapter->getNumber() ] = $chapter->getId();
                }

                $this->FilterVerses( $verses, $langFilter );

                $parallels = new \stdClass;
                $parallels->chapterIds	=
                $parallels->verseCodes	=
                $parallels->texts		= array( );

                foreach( $verses as &$verse ) {
                    $chapterNumber = $verse[ 'chapter_number' ];

                    $chapterId = $chapterNumberToId[ $chapterNumber ];
                    $verse[ 'chapter_id' ] = $chapterId;
                    unset( $verse[ 'chapter_number' ] );

                    if ( isset( $verse[ 'parallel' ] ) ) {
                        $parallels->chapterIds[ $verse[ 'chapter_id' ] ] = $verse[ 'chapter_id' ];
                        $parallels->verseCodes[ $verse[ 'code' ] ] = $verse[ 'code' ];
                        $parallels->texts[ $verse[ 'chapter_id' ] ][ $verse[ 'code' ] ] = array(
                            'verse_id' => 0,
                            'text' => $verse[ 'parallel' ]
                        );
                        unset( $verse[ 'parallel' ] );
                    }
                }


                for ($i = 0; $i<count($verses); $i++) {
                    if (is_array($verses[$i])) {
                        $verse = new Verse();
                        $verse->setBook($verses[$i]['book_id'])
                            ->setLangId($verses[$i]['lang_id'])
                            ->setChapterId($verses[$i]['chapter_id'])
                            ->setCode($verses[$i]['code'])
                            ->setText($verses[$i]['text'])
                            ->setTextClear(null)
                        ;

                        $this->entityManager->persist($verse);
                    }
                }

//                $i = 0;
//                /** @var array $verseArray */
//                foreach ($verses as &$verseArray)
//                {
//                    $i++;
//                    dump($verseArray);
//                    dump($i);
//
//                    $verse = new Verse();
//                    $verse->setBookId($verseArray['book_id']);
//                    $verse->setLangId($verseArray['lang_id']);
//                    $verse->setChapterId($verseArray['chapter_id']);
//                    $verse->setCode($verseArray['code']);
//                    $verse->setText($verseArray['text']);
//
//                    $this->entityManager->persist($verse);
//                }

                $this->entityManager->flush();
            }

            $this->output->writeln('');
        }

        return $this;
    }

    /**
     * Фильтрация стихов
     *
     * для получения итогового текста используется несколько фильтров для каждого языка, для церковнославянского их чуть больше
     * в итоге получится текст, который выводится пользователю в браузере, нужно получить именно его и сохранить в базу
     */
    private function FilterVerses( &$verses, $langFilter ) {
        $progress = new ProgressBar( $this->output );
        $progress->start( count( $verses ) );

        foreach( $verses as &$verse ) {
            $text = $verse[ 'text' ];

            if ( preg_match( '/\/\/(.*)$/', $text, $matches ) ) {
                // параллельные места, по сути ссылка на книги/главы
                // выгрести их надо до применения фильтра, иначе цифры будут поломаны
                $verse[ 'parallel' ] = trim( $matches[ 1 ] );
                $text = preg_replace( '/\/\/.*$/', '', $text );
            }

            $text = $this->ApplyVerseFilter( $text, $langFilter );
            $text = trim( $text );
            $text = html_entity_decode( $text, ENT_COMPAT | ENT_HTML401, 'UTF-8' );
            $verse[ 'text' ] = trim( $text );

            $progress->advance( );
        }

        $progress->finish( );
    }

    /**
     * Применение фильтра
     *
     * особенность в том, что замена текстов затачивалась под однобайтовую кодировку, с которой работает strtr
     * в связи с переносом всех текстов в UTF-8, необходимо преобразовать текст в cp1251, сделать замену, а затем уже обратно в UTF-8
     */
    private function ApplyVerseFilter( $text, $pairs ) {
        if ( $this->langEncoding != 'UTF-8' ) {
            $text = iconv( 'UTF-8', $this->langEncoding, $text );
        }

        $text = strtr( $text, $pairs );

        if ( $this->langEncoding != 'UTF-8' ) {
            $text = iconv( $this->langEncoding, 'UTF-8', $text );
        }

        return $text;
    }

    /**
     * @return array
     */
    private function loadFilter() {
        $ret = array( );
        $replaces = file_get_contents( $this->replaceFilePath );

        if ( $this->langEncoding != 'UTF-8' ) {
            $replaces = iconv( $this->langEncoding, 'UTF-8', $replaces );
        }

        $replaces = preg_split( '/\r\n|\n|\r/', $replaces, NULL, PREG_SPLIT_NO_EMPTY );

        // сборка строк с разбиением на части
        foreach( $replaces as $i => $v ) {
            $sep = preg_quote( mb_substr( $v, 0, 1, 'UTF-8' ), '/' ); // разделитель находится в начале строки

            if ( preg_match( '/'.$sep.'/', $v ) ) {
                $tmp = preg_split( '/'.$sep.'/', $v );

                if ( is_array( $tmp ) && isset( $tmp[ 1 ] ) && ( $tmp[ 1 ] != '' ) ) { // ключ замены не должен быть пустым (пробелы допустимы)
                    $index = preg_replace( array( '/\\\r/', '/\\\n/' ), array( "\r", "\n" ), $tmp[ 1 ] );

                    if ( $this->langEncoding != 'UTF-8' ) {
                        $index = iconv( 'UTF-8', $this->langEncoding, $index );
                        $ret[ $index ] = iconv( 'UTF-8', $this->langEncoding, $tmp[ 2 ] );
                    } else {
                        $ret[ $index ] = $tmp[ 2 ];
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * @return $this
     */
    private function removeExistingLangContent()
    {
        /** @var Chapter $chapter */
        foreach ($this->langChapters as $chapter) {
            $this->entityManager->remove($chapter);
        }

        /** @var Verse $verse */
        foreach ($this->langVerses as $verse) {
            $this->entityManager->remove($verse);
        }

        /** @var VerseOriginNumberings $verseOrderingNumber */
        foreach ($this->langVersesOrderingNumber as $verseOrderingNumber) {
            $this->entityManager->remove($verseOrderingNumber);
        }

        $this->entityManager->flush();

        return $this;
    }

    /**
     * @return $this
     */
    private function getLangContentAndShowInfoMessage()
    {
        $this->langChapters = $this->entityManager->getRepository(Chapter::class)->findBy(['langId' => $this->lang->getId()]);
        $this->langVerses = $this->entityManager->getRepository(Verse::class)->findBy(['langId' => $this->lang->getId()]);
        $this->langVersesOrderingNumber = $this->entityManager->getRepository(VerseOriginNumberings::class)->findBy(['langId' => $this->lang->getId()]);

        $this->output->writeln('глав: '. count($this->langChapters));
        $this->output->writeln('стихов: '. count($this->langVerses));
        $this->output->writeln('оригинальных нумераций: '. count($this->langVersesOrderingNumber));

        return $this;
    }

    /**
     * @return $this
     */
    private function checkAndUpdateLangInDB()
    {
        $lang = $this->entityManager->getRepository(Lang::class)->findOneBy(['code' => $this->langCode]);

        if (!$lang) {
            $newLang = new Lang();

            $newLang
                ->setCode($this->langCode)
                ->setIsoCode($this->langCode)
                ->setTitle($this->langMenuName)
                ->setMenuTitle($this->langOriginalName)
                ->setNumberingTitle($this->langName)
                ->setOrdering($this->langOrdering)
            ;

            $this->entityManager->persist($newLang);
            $this->entityManager->flush();
            $this->lang = $newLang;
        } else {
            $lang
                ->setCode($this->langCode)
                ->setIsoCode($this->langCode)
                ->setTitle($this->langMenuName)
                ->setMenuTitle($this->langOriginalName)
                ->setNumberingTitle($this->langName)
                ->setOrdering($this->langOrdering)
                ;

            $this->entityManager->flush();
            $this->lang = $lang;

        }

        return $this;
    }

    /**
     * @return $this |int
     */
    private function checkOrderingNumber()
    {
        // generate order number
        if (!($this->langOrdering)) {
            $this->langOrdering = $this->entityManager->getRepository(Lang::class)->selectMaxOrdering();

            if (count($this->langOrdering) == 1) {
                $this->langOrdering = $this->langOrdering[0]['max_ordering'] + 1;
            } else {
                return 1;
            }
        }

        return $this;
    }


    /**
     * @param string $fileNamePattern
     * @return string|string[]
     */
    private function prepareFileName(string $fileNamePattern)
    {
        return str_replace('_lang_code', $this->langCode, $fileNamePattern);
    }

    /**
     * @return bool
     */
    private function checkDirAndFilesExists()
    {
        if (!is_dir($this->pathToTextDir)) {
            echo 'No such file or directory ' .  $this->pathToTextDir;
            return false;
        }

        $this->textFilePath = $this->pathToTextDir . DIRECTORY_SEPARATOR . $this->textFile;
        $this->replaceFilePath = $this->pathToTextDir . DIRECTORY_SEPARATOR .  $this->replaceFile;
        $this->numerationFilePath = $this->pathToTextDir . DIRECTORY_SEPARATOR .  $this->numerationFile;

        if (!file_exists($this->textFilePath) || !file_exists($this->replaceFilePath) || !file_exists($this->numerationFilePath)) {
            echo 'Required files are missing. Please check files ' . $this->textFile . ', ' . $this->replaceFile . ' or ' . $this->numerationFile . ' are exists and try again';
            return false;
        }

        return true;
    }
}