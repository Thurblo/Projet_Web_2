<?php

namespace App\Application\Controller;

use App\Domain\User;
use App\Domain\Role;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class ProfileController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $user = $request->getAttribute('user');

        if (!$user) {
            return $response->withHeader('Location', '/Login')->withStatus(302);
        }

        $stats = [];

        if ($user->isAdmin()) {
            // Stats globales pour l'admin
            $stats['nb_etudiants'] = $this->em->getRepository(User::class)
                ->count(['role' => Role::ETUDIANT]);
            $stats['nb_pilotes'] = $this->em->getRepository(User::class)
                ->count(['role' => Role::PILOTE]);
            $stats['nb_offres'] = $this->em->createQuery('SELECT COUNT(o) FROM App\Domain\Offre o')
                ->getSingleScalarResult();
            $stats['nb_entreprises'] = $this->em->createQuery('SELECT COUNT(e) FROM App\Domain\Entreprise e')
                ->getSingleScalarResult();
        }

        if ($user->isPilote()) {
            // Stats pour le pilote
            $stats['nb_etudiants'] = $this->em->getRepository(User::class)
                ->count(['role' => Role::ETUDIANT]);
            $stats['nb_offres'] = $this->em->createQuery('SELECT COUNT(o) FROM App\Domain\Offre o')
                ->getSingleScalarResult();
        }

        return $view->render($response, 'Profile.html.twig', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $request->getAttribute('user');

        if (!$user) {
            return $response->withHeader('Location', '/Login')->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $user->setNom($data['nom'] ?? $user->getNom());
            $user->setPrenom($data['prenom'] ?? $user->getPrenom());
            $user->setEmail($data['email'] ?? $user->getEmail());

            if (!empty($data['password'])) {
                $user->setMotDePasse(password_hash($data['password'], PASSWORD_DEFAULT));
            }

            $this->em->flush();

            return $response->withHeader('Location', '/profile')->withStatus(302);
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, 'Profile.html.twig', [
            'user' => $user,
        ]);
    }
}