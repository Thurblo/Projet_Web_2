<?php

namespace App\Application\Etudiant;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class EtudiantController
{
    
    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'Etudiant.html.twig', [
            'name' => 'John',
        ]);
    }
}