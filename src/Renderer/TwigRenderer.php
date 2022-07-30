<?php

namespace Src\Renderer;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigRenderer
{
    function render(string $view, array $params = [], string $status = Response::HTTP_OK): string|Response
    {
        $loader = new FilesystemLoader('App/Views');
        $twig = new Environment($loader, ['cache' => false, 'debug' => true]);
        $twig->addExtension(new DebugExtension());
        try {
            return (new Response($twig->render($view . '.twig', $params), $status, ['content-type' => 'text/html']))->send();
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            return $e->getMessage();
        }
    }
}
