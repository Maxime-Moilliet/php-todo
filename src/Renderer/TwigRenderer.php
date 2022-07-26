<?php

namespace Src\Renderer;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * @package Src\Renderer
 */
class TwigRenderer
{
    /**
     * @param string $view
     * @param array $params
     * @return void
     */
    public function render(string $view, array $params = [])
    {
        $loader = new FilesystemLoader('App/Views');
        $twig = new Environment($loader, ['cache' => false, 'debug' => true]);
        $twig->addExtension(new DebugExtension());
        try {
            echo $twig->render($view . '.twig', $params);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            // error
        }
    }
}
