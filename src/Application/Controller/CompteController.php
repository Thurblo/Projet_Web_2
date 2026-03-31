<?php

namespace App\Application\Controller;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class CompteController
{
   public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
{
    $view = Twig::fromRequest($request);
    $queryParams = $request->getQueryParams(); 
    
    
    $type = $queryParams['type'] ?? null;

    return $view->render($response, 'Compte.html.twig', [
        'typeCompte' => $type,
    ]);
}
  
public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        
        
        $donnees = $request->getParsedBody();
        $type = $donnees['type'] ?? null;
        $action = $donnees['action'] ?? null;

        if ($action === 'select_type') {
            return $view->render($response, 'Compte.html.twig', [
                'typeCompte' => $type,
            ]);
        }
        
        return $view->render($response, 'Compte.html.twig', [
            'typeCompte' => $type,
        ]);
    }
}