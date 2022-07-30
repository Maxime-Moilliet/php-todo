<?php

namespace App\Controllers;

use App\Models\Todo;
use Src\Controller\Controller;
use Src\Router\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/')]
class TodoController extends Controller
{

    #[Route('')]
    public function index(): string|Response
    {
        $manager = $this->getDatabase()->getManager(Todo::class);
//        $todos = $manager->findAll(['name' => 'text'], ['created_at', 'DESC']);
        $todos = $manager->findAll([], ['created_at', 'DESC']);
        return $this->renderer->render('index', ['todos' => $todos]);
    }

    #[Route('store', method: 'POST')]
    public function store(): RedirectResponse
    {
        $request = Request::createFromGlobals();
        $manager = $this->getDatabase()->getManager(Todo::class);
        $manager->addTodo($request);
        return $this->redirectTo('/');
    }

    #[Route('update/[i:id]', method: 'POST')]
    public function update(int $id): RedirectResponse
    {
        $request = Request::createFromGlobals();
        $manager = $this->getDatabase()->getManager(Todo::class);
        $manager->updateTodo($request, $id);
        return $this->redirectTo('/');
    }

    #[Route('delete/[i:id]', method: 'POST')]
    public function delete(int $id): RedirectResponse
    {
        $manager = $this->getDatabase()->getManager(Todo::class);
        $manager->removeTodo($id);
        return $this->redirectTo('/');
    }
}
