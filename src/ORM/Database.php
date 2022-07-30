<?php

namespace Src\ORM;

use PDO;

class Database
{
    private PDO $pdo;

    private array $managers = [];

    private static ?Database $databaseInstance = null;

    public static function getInstance(): Database
    {
        if (self::$databaseInstance === null) {
            self::$databaseInstance = new Database('127.0.0.1', 'todo', 'root', '');
        }
        return self::$databaseInstance;
    }

    public function __construct(string $host, string $dbName, string $user, string $password)
    {
        $this->pdo = new PDO("mysql:dbname=" . $dbName . ";host=" . $host, $user, $password);
    }

    public function getManager(string $model): mixed
    {
        $managerClass = $model::getManager();
        $this->managers[$model] = $this->managers[$model] ?? new $managerClass($this->pdo, $model);
        return $this->managers[$model];
    }
}
