<?php

namespace Src\ORM;

use PDO;
use Src\Model\Model;

/**
 * @package Src\ORM
 */
class Database
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var array
     */
    private $managers = [];

    /**
     * @var Database
     */
    private static $databaseInstance;

    /**
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$databaseInstance === null) {
            self::$databaseInstance = new Database('127.0.0.1', 'todo', 'root', '');
        }
        return self::$databaseInstance;
    }

    /**
     * @param string $host
     * @param string $dbName
     * @param string $user
     * @param string $password
     */
    public function __construct(string $host, string $dbName, string $user, string $password)
    {
        $this->pdo = new PDO("mysql:dbname=" . $dbName . ";host=" . $host, $user, $password);
    }

    /**
     * @param $model
     * @return mixed
     */
    public function getManager($model)
    {
        $managerClass = $model::getManager();
        $this->managers[$model] = $this->managers[$model] ?? new $managerClass($this->pdo, $model);
        return $this->managers[$model];
    }
}
