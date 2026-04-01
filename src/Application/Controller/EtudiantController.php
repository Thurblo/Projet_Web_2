<?php

namespace App\Application\Controller;

use App\Domain\User;
use App\Domain\Role;
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

        $repo = $this->em->getRepository(User::class);

        if ($nom !== '') {
            $resultats = $repo->createQueryBuilder('u')
                ->where('u.role = :role')
                ->andWhere('u.nom LIKE :nom OR u.prenom LIKE :nom')
                ->setParameter('role', Role::ETUDIANT)
                ->setParameter('nom', '%' . $nom . '%')
                ->getQuery()
                ->getResult();
        } else {
            $resultats = $repo->findBy(['role' => Role::ETUDIANT]);
        }

        return $view->render($response, 'Etudiant-liste.html.twig', [
            'resultats' => $resultats,
            'nom' => $nom,
        ]);
    }

    public function modify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $id = $args['id'];

        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            return $response->withHeader('Location', '/etudiant/liste')->withStatus(302);
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

            return $response->withHeader('Location', '/etudiant/liste')->withStatus(302);
        }

        return $view->render($response, 'Etudiant-modifier.html.twig', [
            'etudiant' => $user,
            'id' => $id,
        ]);
    }

    public function supprimer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $user = $this->em->getRepository(User::class)->find($id);

        if ($user && $user->getRole() === Role::ETUDIANT) {
            $this->em->remove($user);
            $this->em->flush();
        }

        return $response->withHeader('Location', '/etudiant/liste')->withStatus(302);
    }
}