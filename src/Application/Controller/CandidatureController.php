<?php

namespace App\Application\Controller;

use App\Domain\Candidature;
use App\Domain\Offre;
use App\Domain\Role;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class CandidatureController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    // Postuler à une offre
    public function postuler(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $request->getAttribute('user');

        if (!$user || !$user->isEtudiant()) {
            return $response->withHeader('Location', '/Login')->withStatus(302);
        }

        $offreId = (int)$args['id'];
        $offre = $this->em->getRepository(Offre::class)->find($offreId);

        if (!$offre) {
            return $response->withHeader('Location', '/offres')->withStatus(302);
        }

        // Vérifier si déjà postulé
        $existante = $this->em->getRepository(Candidature::class)->findOneBy([
            'etudiant' => $user,
            'offre'    => $offre,
        ]);

        if (!$existante) {
            $candidature = new Candidature($user, $offre);
            $this->em->persist($candidature);
            $this->em->flush();
        }

        return $response->withHeader('Location', '/candidatures')->withStatus(302);
    }

    // Liste des candidatures de l'étudiant
    public function mesCandidatures(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $request->getAttribute('user');

        if (!$user || !$user->isEtudiant()) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $candidatures = $this->em->getRepository(Candidature::class)->findBy(
            ['etudiant' => $user],
            ['datePostulation' => 'DESC']
        );

        $view = Twig::fromRequest($request);
        return $view->render($response, 'Candidatures-liste.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    // Annuler une candidature
    public function annuler(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $request->getAttribute('user');
        $id = (int)$args['id'];

        $candidature = $this->em->getRepository(Candidature::class)->find($id);

        if ($candidature && $candidature->getEtudiant()->getId() === $user->getId()) {
            $this->em->remove($candidature);
            $this->em->flush();
        }

        return $response->withHeader('Location', '/candidatures')->withStatus(302);
    }

    // Liste toutes les candidatures (pilote/admin)
    public function gestion(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $request->getAttribute('user');

        if (!$user || !$user->isAtLeastPilote()) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $candidatures = $this->em->getRepository(Candidature::class)->findBy(
            [],
            ['datePostulation' => 'DESC']
        );

        $view = Twig::fromRequest($request);
        return $view->render($response, 'Candidatures-gestion.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    // Changer le statut d'une candidature
    public function changerStatut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $request->getAttribute('user');

        if (!$user || !$user->isAtLeastPilote()) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $id = (int)$args['id'];
        $candidature = $this->em->getRepository(Candidature::class)->find($id);

        if ($candidature) {
            $data = $request->getParsedBody();
            $statut = $data['statut'] ?? 'en_attente';

            if (in_array($statut, ['en_attente', 'acceptee', 'refusee'])) {
                $candidature->setStatut($statut);
                $this->em->flush();
            }
        }

        return $response->withHeader('Location', '/candidatures/gestion')->withStatus(302);
    }
}