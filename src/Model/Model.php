<?php

namespace Src\Model;

use DateTime;

/**
 * @package Src\Model
 */
abstract class Model
{
    /**
     * @var array
     */
    public $originalData = [];

    /**
     * @return array
     */
    public abstract static function metadata(): array;

    /**
     * @return string
     */
    public abstract static function getManager(): string;

    /**
     * @param array $result
     * @return Model
     * @throws ORMException
     */
    public function hydrate(array $result): Model
    {
        if (empty($result)) {
            throw new ORMException("No results found !");
        }
        $this->originalData = $result;
        foreach ($result as $column => $value) {
            $this->hydrateProperty($column, $value);
        }
        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     */
    private function hydrateProperty(string $column, $value)
    {
        switch ($this::metadata()["columns"][$column]["type"]) {
            case "integer":
                $this->{sprintf("set%s", ucfirst($this::metadata()["columns"][$column]["property"]))}((int)$value);
                break;
            case "string":
                $this->{sprintf("set%s", ucfirst($this::metadata()["columns"][$column]["property"]))}($value);
                break;
            case "datetime":
                $datetime = DateTime::createFromFormat("Y-m-d H:i:s", $value);
                $this->{sprintf("set%s", ucfirst($this::metadata()["columns"][$column]["property"]))}($datetime);
                break;
        }
    }

    /**
     * @param string $column
     * @return ?string
     */
    public function getSQLValueByColumn(string $column): ?string
    {
        $value = $this->{sprintf("get%s", ucfirst($this::metadata()["columns"][$column]["property"]))}();
        if ($value instanceof DateTime) {
            return $value->format("Y-m-d H:i:s");
        }
        return $value;
    }

    /**
     * @param mixed $value
     */
    public function setPrimaryKey($value)
    {
        $this->hydrateProperty($this::metadata()["primaryKey"], $value);
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        $primaryKeyColumn = $this::metadata()["primaryKey"];
        $property = $this::metadata()["columns"][$primaryKeyColumn]["property"];
        return $this->{sprintf("get%s", ucfirst($property))}();
    }
}
