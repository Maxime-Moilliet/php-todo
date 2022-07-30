<?php

namespace Src\Controller;

use Src\ORM\Database;
use Src\Renderer\TwigRenderer;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class Controller
{
    protected TwigRenderer $renderer;

    private Database $database;

    public function __construct()
    {
        $this->renderer = new TwigRenderer();
        $this->database = Database::getInstance();
    }

    protected function getDatabase(): Database
    {
        return $this->database;
    }

    public function redirectTo(string $path): RedirectResponse
    {
        return (new RedirectResponse($path))->send();
    }
}
