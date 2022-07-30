<?php

namespace App\Models;

use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Src\Model\Model;
use App\Managers\TodoManager;

class Todo extends Model
{
    private ?int $id = null;

    private string $name;

    private DateTime $createdAt;

    private DateTime $updatedAt;

    #[ArrayShape(["table" => "string", "primaryKey" => "string", "columns" => "\string[][]"])]
    public static function metadata(): array
    {
        return [
            "table" => "todos",
            "primaryKey" => "id",
            "columns" => [
                "id" => [
                    "type" => "integer",
                    "property" => "id"
                ],
                "name" => [
                    "type" => "string",
                    "property" => "name"
                ],
                "created_at" => [
                    "type" => "datetime",
                    "property" => "createdAt"
                ],
                "updated_at" => [
                    "type" => "datetime",
                    "property" => "updatedAt"
                ]
            ]
        ];
    }

    public static function getManager(): string
    {
        return TodoManager::class;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
