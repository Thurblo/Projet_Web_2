<?php

namespace App\Application\Controller;

use App\Domain\Role;
use App\Domain\User;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class CompteController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $currentUser = $request->getAttribute('user');

        if (!$currentUser || !$currentUser->isAtLeastPilote()) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $view = Twig::fromRequest($request);
        $queryParams = $request->getQueryParams();
        $type = $queryParams['type'] ?? null;

        return $view->render($response, 'Compte.html.twig', [
            'typeCompte' => $type,
            'isAdmin'    => $currentUser->isAdmin(),
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $currentUser = $request->getAttribute('user');

        if (!$currentUser || !$currentUser->isAtLeastPilote()) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $view = Twig::fromRequest($request);

        
        $donnees = $request->getParsedBody();
        $type    = $donnees['type'] ?? null;
        $action  = $donnees['action'] ?? null;

        if ($action === 'select_type') {
            return $view->render($response, 'Compte.html.twig', [
                'typeCompte' => $type,
                'isAdmin'    => $currentUser->isAdmin(),
            ]);
        }

        if ($type !== null && $action !== 'select_type') {
            $targetRole = Role::from($type);

            if (!$currentUser->canCreate($targetRole)) {
                return $response->withHeader('Location', '/')->withStatus(302);
            }

            // Vérifier si l'email existe déjà
            $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $donnees['email'] ?? '']);
            if ($existingUser) {
                return $view->render($response, 'Compte.html.twig', [
                    'typeCompte' => $type,
                    'isAdmin'    => $currentUser->isAdmin(),
                    'error'      => 'Cet email est déjà utilisé.',
                ]);
            }

            $user = new User();
            $user->setNom($donnees['nom'] ?? '');
            $user->setPrenom($donnees['prenom'] ?? '');
            $user->setEmail($donnees['email'] ?? '');
            $user->setMotDePasse(password_hash($donnees['password'] ?? '', PASSWORD_DEFAULT));
            $user->setRole($targetRole);

            $this->em->persist($user);
            $this->em->flush();

            return $response->withHeader('Location', '/compte')->withStatus(302);
        }

        return $view->render($response, 'Compte.html.twig', [
            'typeCompte' => $type,
            'isAdmin'    => $currentUser->isAdmin(),
        ]);
    }
}