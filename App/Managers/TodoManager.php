<?php

namespace App\Managers;

use App\Models\Todo;
use DateTime;
use Src\ORM\Manager;
use Symfony\Component\HttpFoundation\Request;

class TodoManager extends Manager
{
    public function addTodo(Request $request): void
    {
        $todo = new Todo();
        $todo->setName($request->get('name'));
        $todo->setCreatedAt(new DateTime());
        $todo->setUpdatedAt(new DateTime());
        $this->persist($todo);
    }

    public function updateTodo(Request $request, int $id): void
    {
        $todo = $this->find($id);
        $todo->setName($request->get('name'));
        $todo->setUpdatedAt(new DateTime());
        $this->persist($todo);
    }

    public function removeTodo(int $id): void
    {
        $todo = $this->find($id);
        $this->remove($todo);
    }
}
