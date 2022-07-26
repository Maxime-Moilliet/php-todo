<?php

namespace App\Controllers;

use App\Models\Todo;
use DateTime;
use Src\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends Controller
{
    /**
     * @return void
     */
    public function index()
    {
        $manager = $this->getDatabase()->getManager(Todo::class);
//        $todos = $manager->findAll(['name' => 'text'], ['created_at', 'DESC']);
        $todos = $manager->findAll([], ['created_at', 'DESC']);
        return $this->renderer->render('index', ['todos' => $todos]);
    }

    /**
     * @return void
     */
    public function store()
    {
        $request = Request::createFromGlobals();
        $manager = $this->getDatabase()->getManager(Todo::class);
        $todo = new Todo();
        $todo->setName($request->get('name'));
        $todo->setCreatedAt(new DateTime());
        $todo->setUpdatedAt(new DateTime());
        $manager->persist($todo);
        return header('Location: /');
    }

    /**
     * @param int $id
     * @return void
     */
    public function update(int $id)
    {
        $request = Request::createFromGlobals();
        $manager = $this->getDatabase()->getManager(Todo::class);
        $todo = $manager->find($id);
        $todo->setName($request->get('name'));
        $todo->setUpdatedAt(new DateTime());
        $manager->persist($todo);
        return header('Location: /');
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        $manager = $this->getDatabase()->getManager(Todo::class);
        $todo = $manager->find($id);
        $manager->remove($todo);
        return header('Location: /');
    }
}
