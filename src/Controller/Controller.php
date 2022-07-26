<?php

namespace Src\Controller;

use Src\ORM\Database;
use Src\Renderer\TwigRenderer;

/**
 * @package Src\Controller
 */
class Controller
{
    /**
     * @var TwigRenderer
     */
    protected $renderer;

    /**
     * @var Database
     */
    private $database;

    public function __construct()
    {
        $this->renderer = new TwigRenderer();
        $this->database = Database::getInstance();
    }

    /**
     * @return Database
     */
    protected function getDatabase(): Database
    {
        return $this->database;
    }
}
