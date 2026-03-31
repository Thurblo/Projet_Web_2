<?php

namespace App\Application\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class EtudiantController
{
    public function modify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $id = $args['id'];

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $nom = $data['nom'] ?? '';
            $prenom = $data['prenom'] ?? '';
            $campus = $data['campus'] ?? '';
            $description = $data['description'] ?? '';
        }

        return $view->render($response, 'Etudiant-modifier.html.twig', [
            'id' => $id,
        ]);
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        $params = $request->getQueryParams();
        $nom = $params['nom'] ?? '';

        $resultats = [];

        return $view->render($response, 'Etudiant-liste.html.twig', [
            'resultats' => $resultats,
            'nom' => $nom,
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'Etudiant-creer.html.twig');
    }
}