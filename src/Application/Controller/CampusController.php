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

        return $view->render($response, 'Campus-voir.html.twig', [
            'campus' => $campus,
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

        $etudiants = $this->em->getRepository(User::class)->findBy(['role' => Role::ETUDIANT]);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $etudiantId = (int)($data['etudiant_id'] ?? 0);
            $etudiant = $this->em->getRepository(User::class)->find($etudiantId);

            if ($etudiant && $etudiant->getRole() === Role::ETUDIANT) {
                $etudiant->setCampus($campus);
                $this->em->flush();
                return $response->withHeader('Location', '/campus/voir/' . $campus->getId())->withStatus(302);
            }
        }

        return $view->render($response, 'Campus-rattacher.html.twig', [
            'campus'    => $campus,
            'etudiants' => $etudiants,
        ]);
    }
}