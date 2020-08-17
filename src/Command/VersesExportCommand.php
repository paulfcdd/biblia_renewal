<?php


namespace App\Command;


use App\Command\DatabaseMapper\AbstractMapper;
use App\Command\DatabaseMapper\VerseMapper;
use App\Command\Traits\MapperInstanceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class VersesExportCommand extends Command
{
    private $entityManager;
    private $connection;
    private $targetDb;
    private $sourceDb;
    private $appKernel;
    /** @var OutputInterface */
    private $output;
    /** @var InputInterface */
    private $input;
    private $verses;


    public function __construct(EntityManagerInterface $entityManager, KernelInterface $kernel)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->connection = $this->entityManager->getConnection();
        $this->appKernel = $kernel;
    }

    public function configure()
    {
        $this->setName('app:verses-import');
        $this->addArgument('source_db', InputArgument::REQUIRED, 'Имя базы данных из которой будут импортироваться данные');
        $this->addArgument('target_db', InputArgument::REQUIRED, 'Имя базы данных в которую будут импортироваться данные');

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceDb = $input->getArgument('source_db');
        $targetDb = $input->getArgument('target_db');
        $this->input = $input;
        $this->output = $output;
        $this->verses = $this->getVersesFromSource($sourceDb);

        $projectDir = $this->appKernel->getProjectDir() . DIRECTORY_SEPARATOR . 'verses_dir';
        $this->checkDir($projectDir);
        $this->importDataToCsv($sourceDb, $projectDir);

        return 0;
    }

    /**
     * @param string $dirName
     * @return $this
     */
    private function checkDir(string $dirName)
    {
        if (!is_dir($dirName)) {
            mkdir($dirName);
        }

        return $this;
    }

    private function importDataToCsv(string $sourceDb, string $saveDir)
    {
        $spreadSheet = new Spreadsheet();
//        $this->writeVersesToFile($spreadSheet, $sourceDb, $saveDir);
//        $this->writeVersesTranslationsToFile($spreadSheet, $sourceDb, $saveDir);
        $this->writeVersesCrossReferenceToFile($spreadSheet, $sourceDb, $saveDir);
    }

    private function getVersesFromSource(string $sourceDb)
    {
        $sql = "SELECT * FROM $sourceDb.verses";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function writeVersesToFile(Spreadsheet $spreadSheet, string $sourceDb, string $saveDir)
    {
        $this->output->writeln('<info>EXPORT VERSES TO FILE</info>');
        $sheet = $spreadSheet->getActiveSheet();
        $writer = new Csv($spreadSheet);
        $progressBar = new ProgressBar($this->output, count($this->verses));

        $writer->setDelimiter(',');
        $row = 1;

        $sheet->setCellValueByColumnAndRow(1, $row, 'Chapter ID');
        $sheet->setCellValueByColumnAndRow(2, $row, 'Code');
        $sheet->setCellValueByColumnAndRow(3, $row, 'Uverse');

        for ($i = 0; $i < count($this->verses); $i++) {
            $row++;
            $sheet->setCellValueByColumnAndRow(1, $row, $this->verses[$i]['chapter_id']);
            $sheet->setCellValueByColumnAndRow(2, $row, $this->verses[$i]['code']);
            $sheet->setCellValueByColumnAndRow(3, $row, $this->verses[$i]['uverse']);
            $progressBar->advance();
        }

        $this->output->writeln('');
        $progressBar->finish();

        $writer->save($saveDir . DIRECTORY_SEPARATOR . 'verses.csv');
    }

    private function writeVersesTranslationsToFile(Spreadsheet $spreadSheet, string $sourceDb, string $saveDir)
    {
        $this->output->writeln('<info>EXPORT VERSES TRANSLATIONS TO FILE</info>');
        $sheet = $spreadSheet->getActiveSheet();
        $writer = new Csv($spreadSheet);
        $progressBar = new ProgressBar($this->output, count($this->verses));

        $writer->setDelimiter(',');
        $row = 1;

        $sheet->setCellValueByColumnAndRow(1, $row, 'Verse ID');
        $sheet->setCellValueByColumnAndRow(2, $row, 'Lang ID');
        $sheet->setCellValueByColumnAndRow(3, $row, 'Original text');
        $sheet->setCellValueByColumnAndRow(4, $row, 'Prepared text');

        for ($i = 0; $i < count($this->verses); $i++) {
            $row++;
            $sheet->setCellValueByColumnAndRow(1, $row, $this->verses[$i]['id']);
            $sheet->setCellValueByColumnAndRow(2, $row, $this->verses[$i]['lang_id']);
            $sheet->setCellValueByColumnAndRow(3, $row, $this->verses[$i]['text']);
            $sheet->setCellValueByColumnAndRow(4, $row, $this->verses[$i]['text_clear']);
            $progressBar->advance();
        }

        $this->output->writeln('');
        $progressBar->finish();

        $writer->save($saveDir . DIRECTORY_SEPARATOR . 'verse_translation.csv');
    }

    private function writeVersesCrossReferenceToFile(Spreadsheet $spreadSheet, string $sourceDb, string $saveDir)
    {
        $sourceTableData = $this->getSourceVerseCrossRef($sourceDb);
        $this->output->writeln('<info>EXPORT VERSES CROSS REFS TO FILE</info>');
        $sheet = $spreadSheet->getActiveSheet();
        $writer = new Csv($spreadSheet);
        $progressBar = new ProgressBar($this->output, count($sourceTableData));

        $writer->setDelimiter(',');
        $row = 1;

        $sheet->setCellValueByColumnAndRow(1, $row, 'ID');
        $sheet->setCellValueByColumnAndRow(2, $row, 'Verse From');
        $sheet->setCellValueByColumnAndRow(3, $row, 'Verse To');

        for ($i = 0; $i < count($sourceTableData); $i++) {
            $row++;
            $sheet->setCellValueByColumnAndRow(1, $row, $sourceTableData[$i]['id']);
            $sheet->setCellValueByColumnAndRow(2, $row, $sourceTableData[$i]['uverse_from']);
            $sheet->setCellValueByColumnAndRow(3, $row, $sourceTableData[$i]['uverse_to']);
            $progressBar->advance();
        }

        $this->output->writeln('');
        $progressBar->finish();

        $writer->save($saveDir . DIRECTORY_SEPARATOR . 'verse_cross_refs.csv');
    }

    private function getSourceVerseCrossRef(string $sourceDb)
    {
        $sql = "SELECT * FROM $sourceDb.verse_crossrefs";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
