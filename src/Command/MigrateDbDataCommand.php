<?php

namespace App\Command;

ini_set('memory_limit', -1);

use App\Command\DatabaseMapper\AbstractMapper;
use App\Command\DatabaseMapper\BookGroupMapper;
use App\Command\DatabaseMapper\BookGroupTranslationMapper;
use App\Command\DatabaseMapper\BookMapper;
use App\Command\DatabaseMapper\BookTranslationMapper;
use App\Command\DatabaseMapper\ChapterMapper;
use App\Command\DatabaseMapper\InterpretationMapper;
use App\Command\DatabaseMapper\LangMapper;
use App\Command\DatabaseMapper\MainPageLabelsMapper;
use App\Command\DatabaseMapper\UserMapper;
use App\Command\DatabaseMapper\VerseCrossRefMapper;
use App\Command\DatabaseMapper\VerseMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MigrateDbDataCommand extends Command
{
    public const SECTIONS = [
        [
            'title' => 'Новый Завет',
            'code' => 'new_testament',
        ],
        [
            'title' => 'Ветхий Завет',
            'code' => 'old_testament',
        ]
    ];

    public const SETTINGS = [
        'default_lang' => 'r',
    ];

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UserPasswordEncoderInterface  */
    private $encoder;
    private $input;
    private $output;
    private $targetDb;
    private $sourceDb;


    /**
     * MigrateDbDataCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @param string|null $name
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, string $name = null)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function configure()
    {
        $this
            ->setName('app:data:migrate')
            ->setDescription('Test')
            ->addArgument('source_db', InputArgument::REQUIRED, 'Имя базы данных из которой будут импортироваться данные')
            ->addArgument('target_db', InputArgument::REQUIRED, 'Имя базы данных в которую будут импортироваться данные')
            ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws DBALException
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new \DateTime();
        $this->input = $input;
        $this->output = $output;
        $this->sourceDb = $input->getArgument('source_db');
        $this->targetDb = $input->getArgument('target_db');

        $this->output->writeln('Импорт начат в ' . $date->format('H:i:s d.m.Y'));
        $this->output->writeln('<info>Заполнение таблицы Заветов</info>');

        $disableForeignKeyChecks = "SET FOREIGN_KEY_CHECKS=0;";
        $this->entityManager->getConnection()->prepare($disableForeignKeyChecks)->execute();

        $this->fillSectionTable();
        $this->fillSettingsTable();
        $this->createMapperInstance(LangMapper::class)->run();
        $this->createMapperInstance(MainPageLabelsMapper::class)->run();
        $this->createMapperInstance(BookGroupMapper::class)->run();
        $this->createMapperInstance(BookGroupTranslationMapper::class)->run();
        $this->createMapperInstance(BookMapper::class)->run();
        $this->createMapperInstance(BookTranslationMapper::class)->run();
        $this->createMapperInstance(ChapterMapper::class)->run();
        $this->createMapperInstance(InterpretationMapper::class)->run();
        $this->createMapperInstance(UserMapper::class)->run();
        $this->createMapperInstance(VerseMapper::class)->run();
        $this->createMapperInstance(VerseCrossRefMapper::class)->run();

        $enableForeignKeyChecks = "SET FOREIGN_KEY_CHECKS=1;";
        $this->entityManager->getConnection()->prepare($enableForeignKeyChecks)->execute();

        $finish = new \DateTime();

        $output->writeln('<info>КОНЕЦ ИМПОРТА ТАБЛИЦ</info>');
        $this->output->writeln('Импорт закончен в ' . $finish->format('H:i:s d.m.Y'));
        return 0;
    }

    /**
     * @param string $mapperFQN
     * @return AbstractMapper
     */
    private function createMapperInstance(string $mapperFQN)
    {
        return new $mapperFQN($this->entityManager, $this->input, $this->output, $this->targetDb, $this->sourceDb, $this->encoder);
    }

    /**
     * @return $this
     * @throws DBALException
     */
    public function fillSectionTable()
    {
        foreach (self::SECTIONS as $section) {
            $query = "INSERT INTO ".$this->targetDb.".section VALUES (
                    NULL, 
                    '".str_replace("'", "''", $section['title'])."', 
                    '".str_replace("'", "''", $section['code'])."'
                    )";

            $this->entityManager->getConnection()->prepare($query)->execute();
        }

        return $this;
    }

    /**
     * @return $this
     * @throws DBALException
     */
    private function fillSettingsTable()
    {
        foreach (self::SETTINGS as $key => $value) {
            $query = "INSERT INTO ".$this->targetDb.".system_settings VALUES (
                    NULL, 
                    '".str_replace("'", "''", $key)."', 
                    '".str_replace("'", "''", $value)."'
                    )";

            $this->entityManager->getConnection()->prepare($query)->execute();
        }

        return $this;
    }
}
