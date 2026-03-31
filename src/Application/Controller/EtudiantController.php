<?php

namespace App\Application\Controller;

use App\Domain\Etudiant;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class EtudiantController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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

        $success = false;
        $nom = '';
        $prenom = '';
        $email = '';
        $telephone = '';
        $campus = '';
        $promo = '';
        $formation = '';
        $description = '';

        if ($request->getMethod() === 'POST') {
            $parsedBody = $request->getParsedBody();

            $nom = trim($parsedBody['nom'] ?? '');
            $prenom = trim($parsedBody['prenom'] ?? '');
            $email = trim($parsedBody['email'] ?? '');
            $telephone = trim($parsedBody['telephone'] ?? '');
            $campus = trim($parsedBody['campus'] ?? '');
            $promo = trim($parsedBody['promo'] ?? '');
            $formation = trim($parsedBody['formation'] ?? '');
            $description = trim($parsedBody['description'] ?? '');

            if ($nom !== '' && $prenom !== '' && $email !== '') {
                $nouvelEtudiant = new Etudiant(
                    $nom,
                    $prenom,
                    $email,
                    $telephone,
                    $campus,
                    $promo,
                    $formation,
                    $description
                );

                $this->em->persist($nouvelEtudiant);
                $this->em->flush();

                $success = true;
            }
        }

        return $view->render($response, 'etudiant-creer.html.twig', [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'campus' => $campus,
            'promo' => $promo,
            'formation' => $formation,
            'description' => $description,
            'success' => $success,
        ]);
    }

    public function modify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $id = $args['id'];

        return $view->render($response, 'Etudiant-modifier.html.twig', [
            'id' => $id,
        ]);
    }
}