<?php


namespace App\Command;


use App\Entity\Testament;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Yaml\Yaml;

class SetupCommand extends Command
{
    private const DOCTRINE_DB_IMPORT_COMMAND = 'doctrine:database:import';

    /** @var string */
//    protected static $defaultName = 'app:setup';
    /** @var Yaml */
    private $yaml;
    /** @var ConfigurationInterface */
    private $container;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var Command */
    private $command;
    /** @var array */
    private $setupFile;
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var string */
    private $dumpFolder;
    /** @var string */
    private $dumpFileExtension;

    public function __construct(Yaml $yaml, Container $container, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->yaml = $yaml;
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    public function configure()
    {
        $this
            ->setName('app:setup')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        $this->input = $input;
        $this->output = $output;
        $appSetup = $this->container->getParameter('app_setup');
        $this->setupFile = $this->yaml::parseFile($appSetup['setup_file']);
        $this->dumpFolder = $appSetup['dump_folder'];
        $this->command = $this->getApplication()->find(self::DOCTRINE_DB_IMPORT_COMMAND);
        $this->dumpFileExtension = $this->setupFile['dump']['_extension'];

        $this
            ->importTestamentsTable()
            ->importBookGroupsTable()
        ;

        $output->writeln('<info>Настройка приложения завершена!</info>');

        return 0;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function importTestamentsTable()
    {
        $schema = $this->setupFile['dump']['testament'];
        $file = $this->dumpFolder . DIRECTORY_SEPARATOR . $schema . $this->dumpFileExtension;

//        $connection = $this->entityManager->getConnection();
//        $platform = $connection->getDatabasePlatform();
//        $connection->executeUpdate($platform->getTruncateTableSQL($schema));

        $arguments = [
            'command' => self::DOCTRINE_DB_IMPORT_COMMAND,
            'file' => $file,
        ];

        $arrayInput = new ArrayInput($arguments);
        $this->command->run($arrayInput, $this->output);

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function importBookGroupsTable()
    {
        $schema = $this->setupFile['dump']['book_groups'];
        $file = $this->dumpFolder . DIRECTORY_SEPARATOR . $schema . $this->dumpFileExtension;

        $arguments = [
            'command' => self::DOCTRINE_DB_IMPORT_COMMAND,
            'file' => $file,
        ];

        $arrayInput = new ArrayInput($arguments);
        $this->command->run($arrayInput, $this->output);

        return $this;
    }

    /**
     * @return Command
     */
    private function dbImportCommand()
    {
        return $this->getApplication()->find(self::DOCTRINE_DB_IMPORT_COMMAND);
    }
}