<?php

namespace App\Application\Controller;

use App\Domain\Campus;
use App\Domain\User;
use App\Domain\Role;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class CampusController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $campus = $this->em->getRepository(Campus::class)->findAll();

        return $view->render($response, 'Campus-liste.html.twig', [
            'campus' => $campus,
        ]);
    }

    public function creer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $ville = trim($data['ville'] ?? '');
            $nom   = trim($data['nom'] ?? '');

            if ($ville !== '') {
                $campus = new Campus($ville, $nom);
                $this->em->persist($campus);
                $this->em->flush();
                return $response->withHeader('Location', '/campus/liste')->withStatus(302);
            }
        }

        return $view->render($response, 'Campus-creer.html.twig');
    }

    public function modifier(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $id = (int)$args['id'];
        $campus = $this->em->getRepository(Campus::class)->find($id);

        if (!$campus) {
            return $response->withHeader('Location', '/campus/liste')->withStatus(302);
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $campus->setVille(trim($data['ville'] ?? $campus->getVille()));
            $campus->setNom(trim($data['nom'] ?? $campus->getNom()));
            $this->em->flush();
            return $response->withHeader('Location', '/campus/liste')->withStatus(302);
        }

        return $view->render($response, 'Campus-modifier.html.twig', [
            'campus' => $campus,
        ]);
    }

    public function supprimer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)$args['id'];
        $campus = $this->em->getRepository(Campus::class)->find($id);

        if ($campus) {
            $this->em->remove($campus);
            $this->em->flush();
        }

        return $response->withHeader('Location', '/campus/liste')->withStatus(302);
    }

    public function voir(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $id = (int)$args['id'];
        $campus = $this->em->getRepository(Campus::class)->find($id);

        if (!$campus) {
            return $response->withHeader('Location', '/campus/liste')->withStatus(302);
        }

        // Récupérer les pilotes rattachés à ce campus
        $pilotes = $this->em->getRepository(User::class)->findBy([
            'campus' => $campus,
            'role'   => Role::PILOTE,
        ]);

        return $view->render($response, 'Campus-voir.html.twig', [
            'campus'  => $campus,
            'pilotes' => $pilotes,
        ]);
    }

    public function rattacher(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $id = (int)$args['id'];
        $campus = $this->em->getRepository(Campus::class)->find($id);

        if (!$campus) {
            return $response->withHeader('Location', '/campus/liste')->withStatus(302);
        }

        // Déterminer le type depuis l'URL ou le POST
        $type = $request->getQueryParams()['type'] ?? 'etudiant';

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $type = $data['type'] ?? 'etudiant';
            $userId = (int)($data['user_id'] ?? 0);
            $user = $this->em->getRepository(User::class)->find($userId);

            $expectedRole = $type === 'pilote' ? Role::PILOTE : Role::ETUDIANT;

            if ($user && $user->getRole() === $expectedRole) {
                $user->setCampus($campus);
                $this->em->flush();
                return $response->withHeader('Location', '/campus/voir/' . $campus->getId())->withStatus(302);
            }
        }

        // Récupérer la liste selon le type
        $role = $type === 'pilote' ? Role::PILOTE : Role::ETUDIANT;
        $users = $this->em->getRepository(User::class)->findBy(['role' => $role]);

        return $view->render($response, 'Campus-rattacher.html.twig', [
            'campus' => $campus,
            'users'  => $users,
            'type'   => $type,
        ]);
    }
}