<?php

require_once(__DIR__ . '/DatabaseConfig.php');
require_once(__DIR__ . '/Statements/DeleteStatement.php');
require_once(__DIR__ . '/Statements/InsertStatement.php');
require_once(__DIR__ . '/Statements/SelectStatement.php');
require_once(__DIR__ . '/Statements/UpdateStatement.php');

class Database {

    private static $db = null;
    private $pdo;

    // Singleton
    private function __construct(DatabaseConfig $config)
    {
        $dsn = "mysql:host=$config->host;dbname=$config->database";
        $this->pdo = new \PDO($dsn, $config->user, $config->pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public static function with(DatabaseConfig $config)
    {
        if (self::$db == null)
        {
            self::$db = new Database($config);
        }
        return self::$db;
    }

    public function insertInto(string $table, array $data): InsertStatement
    {
        $query = "INSERT INTO $table\n";

        $qCols = '';
        $qVals = '';

        foreach ($data as $k => $v) {
            $qCols .= (empty($qCols) ? '' : ', ') . $k;
            $qVals .= (empty($qVals) ? '' : ', ') . ":$k";
        }

        $query .= "($qCols) ";
        $query .= "VALUES($qVals)";

        return new InsertStatement($this->pdo, $query, $data);
    }

    public function update(string $table, array $data): UpdateStatement
    {
        $query = "UPDATE $table\n";
        $sets = "SET\n";

        foreach ($data as $k => $v) {
            $sets .= " $k = :$k,\n";
        }

        $query .= substr($sets, 0, -2) . "\n";

        return new UpdateStatement($this->pdo, $query, $data);
    }

    public function deleteFrom(string $table): DeleteStatement
    {
        $query = "DELETE\nFROM $table\n";
        return new DeleteStatement($this->pdo, $query);
    }

    public function selectFrom(string $table, array $columns = []): SelectStatement
    {
        $qCols = empty($columns) ? '*' : implode(',', $columns);
        $query = "SELECT $qCols \nFROM $table";
        return new SelectStatement($this->pdo, $query);
    }
    
}
