<?php


namespace App\Command\DatabaseMapper;


use App\Entity\Lang;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\{
    Connection, DBALException
};
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class AbstractMapper
{
    /** @var string */
    public const TEMP_TABLE_PREFIX = 'tmp_';
    /** @var string[] */
    public const TEMP_TABLE_NAMES = [
        'book_group', 'book', 'chapter', 'verse', 'lang', 'interpretation'
    ];
    /** @var EntityManagerInterface */
    public $entityManager;
    /** @var string */
    public $targetDb;
    /** @var string */
    public $sourceDb;
    /** @var InputInterface */
    public $input;
    /** @var OutputInterface */
    public $output;
    /** @var Connection */
    protected $connection;
    protected $encoder;

    /**
     * AbstractMapper constructor.
     * @param EntityManagerInterface $entityManager
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $targetDb
     * @param string $sourceDb
     * @throws DBALException
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        InputInterface $input,
        OutputInterface $output,
        string $targetDb,
        string $sourceDb,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->entityManager = $entityManager;
        $this->targetDb = $targetDb;
        $this->sourceDb = $sourceDb;
        $this->input = $input;
        $this->output = $output;
        $this->encoder = $encoder;
        $this->connection = $this->entityManager->getConnection();
        $this->createTmpTableMap();
    }

    /**
     * @return mixed
     */
    public abstract function run();

    /**
     * @param string $sourceTableName
     * @return mixed[]
     * @throws DBALException
     */
    public function getSourceTableData(string $sourceTableName)
    {
        $sql = "SELECT * FROM $this->sourceDb.$sourceTableName";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param string $tableName
     * @param int $oldId
     * @param int $newId
     * @return $this
     * @throws DBALException
     */
    public function fillTmpTableMap(string $tableName, int $oldId, int $newId)
    {
        $tableName = $this->targetDb . '.' .self::TEMP_TABLE_PREFIX . $tableName;
        $query = "INSERT INTO $tableName VALUES(NULL, $oldId, $newId)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        return $this;
    }

    /**
     * @param string $tableName
     * @param string $oldId
     * @return mixed
     * @throws DBALException
     */
    public function getNewIdFromTmpTableMap(string $tableName, string $oldId)
    {
        $tableName = self::TEMP_TABLE_PREFIX . $tableName;
        $query = "SELECT new_id FROM ".$this->targetDb.".$tableName WHERE old_id = $oldId";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param array $oldDbTitle
     * @return \Doctrine\DBAL\Driver\Statement|null
     * @throws DBALException
     */
    public function getMappedLang(array $oldDbTitle)
    {
        $importedLand = $this->getNewIdFromTmpTableMap('lang', $oldDbTitle['lang_id']);

        if (!is_bool($importedLand)) {
            $query = 'SELECT id FROM ' . $this->targetDb . '.lang WHERE id=' . $importedLand['new_id'] . ';';

            return $this->connection->query($query)->fetch();
        }

        return null;

    }


    /**
     * @return $this
     * @throws DBALException
     */
    private function createTmpTableMap()
    {
        foreach (self::TEMP_TABLE_NAMES as $tableName) {
            $tableName = $this->targetDb . '.' .self::TEMP_TABLE_PREFIX . $tableName;
            $query = "CREATE TABLE IF NOT EXISTS $tableName (id int(6) AUTO_INCREMENT PRIMARY KEY, old_id int(6), new_id int(6))";
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
        }

        return $this;
    }
}
