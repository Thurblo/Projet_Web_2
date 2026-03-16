<?php

namespace App\Application\Controller;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class HomeController
{
    
    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'home.html.twig', [
            'name' => 'John',
        ]);
    }

    public function connexion(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'Connexion.html.twig', [
            'name' => 'John',
        ]);
    }

    public function mention(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
    
        return $view->render($response, '/mentions.html.twig', [
            'name' => 'John',
        ]);
    }
}