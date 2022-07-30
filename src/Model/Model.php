<?php

namespace Src\Model;

use DateTime;
use Src\ORM\ORMException;

abstract class Model
{
    public array $originalData = [];

    public abstract static function metadata(): array;

    public abstract static function getManager(): string;

    /**
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

    private function hydrateProperty(string $column, $value): void
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

    public function getSQLValueByColumn(string $column): ?string
    {
        $value = $this->{sprintf("get%s", ucfirst($this::metadata()["columns"][$column]["property"]))}();
        if ($value instanceof DateTime) {
            return $value->format("Y-m-d H:i:s");
        }
        return $value;
    }

    public function setPrimaryKey(mixed $value): void
    {
        $this->hydrateProperty($this::metadata()["primaryKey"], $value);
    }

    public function getPrimaryKey(): mixed
    {
        $primaryKeyColumn = $this::metadata()["primaryKey"];
        $property = $this::metadata()["columns"][$primaryKeyColumn]["property"];
        return $this->{sprintf("get%s", ucfirst($property))}();
    }
}
