<?php

namespace Src\ORM;

use PDO;
use ReflectionException;
use Src\Model\Model;

/**
 * @package Src\ORM
 */
class Manager
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    private $model;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @param PDO $pdo
     * @param $model
     * @throws ORMException
     * @throws ReflectionException
     */
    public function __construct(PDO $pdo, $model)
    {
        $this->pdo = $pdo;
        $reflectionClass = new \ReflectionClass($model);
        if ($reflectionClass->getParentClass()->getName() == Model::class) {
            $this->model = $model;
            $this->metadata = $this->model::metadata();
        } else {
            throw new ORMException("Cette classe n'est pas une entité.");
        }
        $this->model = $model;
    }

    /**
     * @param $property
     * @return int|string|null
     */
    public function getColumnByProperty($property)
    {
        $property = lcfirst($property);
        $columns = array_keys(array_filter($this->metadata["columns"], function ($column) use ($property) {
            return $column["property"] == $property;
        }));
        return array_shift($columns);
    }

    /**
     * @param array $filters
     * @return string
     */
    private function where(array $filters = []): string
    {
        if(!empty($filters)) {
            $conditions = [];
            foreach($filters as $property => $value) {
                $conditions[] = sprintf("%s = :%s",$this->getColumnByProperty($property), $property);
            }
            return sprintf("WHERE %s", implode(" AND ", $conditions));
        }
        return "";
    }

    /**
     * @param array $sorting
     * @return string
     */
    private function orderBy(array $sorting = []): string
    {
        if (!empty($sorting)) {
            $sorts = [];
            foreach ($sorting as $property => $value) {
                $sorts[] = sprintf("%s %s", $this->getColumnByProperty($property), $value);
            }
            return sprintf("ORDER BY %s", implode("", $sorts));
        }
        return "";
    }

    /**
     * @param array $filters
     * @return Model
     */
    public function fetch(array $filters = []): Model
    {
        $sqlQuery = sprintf("SELECT * FROM %s %s LIMIT 0,1", $this->metadata["table"], $this->where($filters));
        $statement = $this->pdo->prepare($sqlQuery);
        $statement->execute($filters);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return (new $this->model())->hydrate($result);
    }

    /**
     * @param array $filters
     * @param array $orderBy
     * @return array
     */
    public function fetchAll(array $filters = [], array $orderBy = []): array
    {
        $sqlQuery = sprintf("SELECT * FROM %s %s %s", $this->metadata["table"], $this->where($filters), $this->orderBy($orderBy));
        $statement = $this->pdo->prepare($sqlQuery);
        $statement->execute($filters);
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($results as $result) {
            $data[] = (new $this->model())->hydrate($result);
        }
        return $data;
    }

    /**
     * @param array $filters
     * @return Model
     */
    public function findOneBy(array $filters = []): Model
    {
        return $this->fetch($filters);
    }

    /**
     * @param mixed $id
     * @return Model
     */
    public function find($id): Model
    {
        return $this->fetch([$this->metadata["primaryKey"] => $id]);
    }

    /**
     * @param ?array $filters
     * @param ?array $orderBy
     * @return array
     */
    public function findAll(?array $filters, ?array $orderBy): array
    {
        return $this->fetchAll($filters, $orderBy);
    }

    /**
     * @param Model $model
     * @return void
     */
    public function persist(Model $model)
    {
        if ($model->getPrimaryKey()) {
            $this->update($model);
        } else {
            $this->insert($model);
        }
    }

    /**
     * @param Model $model
     * @return void
     */
    private function update(Model $model)
    {
        $set = [];
        $parameters = [];
        foreach (array_keys($this->metadata["columns"]) as $column) {
            $sqlValue = $model->getSQLValueByColumn($column);
            if ($sqlValue !== $model->originalData[$column]) {
                $parameters[$column] = $sqlValue;
                $model->orignalData[$column] = $sqlValue;
                $set[] = sprintf("%s = :%s", $column, $column);
            }
        }
        if (count($set)) {
            $sqlQuery = sprintf("UPDATE %s SET %s WHERE %s = :id", $this->metadata["table"], implode(", ", $set), $this->metadata["primaryKey"]);
            $statement = $this->pdo->prepare($sqlQuery);
            $statement->execute(array_merge(["id" => $model->getPrimaryKey()], $parameters));
        }
    }

    /**
     * @param Model $model
     * @return void
     */
    private function insert(Model $model)
    {
        $set = [];
        $parameters = [];
        foreach (array_keys($this->metadata["columns"]) as $column) {
            $sqlValue = $model->getSQLValueByColumn($column);
            $model->orignalData[$column] = $sqlValue;
            $parameters[$column] = $sqlValue;
            $set[] = sprintf("%s = :%s", $column, $column);
        }
        $sqlQuery = sprintf("INSERT INTO %s SET %s", $this->metadata["table"], implode(",", $set));
        $statement = $this->pdo->prepare($sqlQuery);
        $statement->execute($parameters);
        $model->setPrimaryKey($this->pdo->lastInsertId());
    }

    /**
     * @param Model $model
     * @return void
     */
    public function remove(Model $model)
    {
        $sqlQuery = sprintf("DELETE FROM %s WHERE %s = :id", $this->metadata["table"], $this->metadata["primaryKey"]);
        $statement = $this->pdo->prepare($sqlQuery);
        $statement->execute(["id" => $model->getPrimaryKey()]);
    }
}
