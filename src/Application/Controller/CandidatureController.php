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

        $view = Twig::fromRequest($request);

        $existante = $this->em->getRepository(Candidature::class)->findOneBy([
            'etudiant' => $user,
            'offre'    => $offre,
        ]);

        if ($request->getMethod() === 'GET') {
            return $view->render($response, 'Candidatures-postuler.html.twig', [
                'offre'       => $offre,
                'dejaPostule' => $existante !== null,
            ]);
        }

        // POST
        if ($existante) {
            return $view->render($response, 'Candidatures-postuler.html.twig', [
                'offre'       => $offre,
                'dejaPostule' => true,
            ]);
        }

        $data    = $request->getParsedBody();
        $files   = $request->getUploadedFiles();

        $cvTexte  = $data['cv_texte'] ?? '';
        $lmTexte  = $data['lm_texte'] ?? '';
        $cvFichier = $files['cv'] ?? null;
        $lmFichier = $files['lettre_motivation'] ?? null;

        $cvRempli = ($cvFichier && $cvFichier->getSize() > 0) || trim($cvTexte) !== '';
        $lmRempli = ($lmFichier && $lmFichier->getSize() > 0) || trim($lmTexte) !== '';

        if (!$cvRempli || !$lmRempli) {
            $erreurs = [];
            if (!$cvRempli) $erreurs[] = 'Veuillez fournir votre CV (fichier ou texte).';
            if (!$lmRempli) $erreurs[] = 'Veuillez fournir votre lettre de motivation (fichier ou texte).';

            return $view->render($response, 'Candidatures-postuler.html.twig', [
                'offre'       => $offre,
                'erreurs'     => $erreurs,
                'dejaPostule' => false,
            ]);
        }

        $candidature = new Candidature($user, $offre);
        $candidature->setCommentaire($data['commentaire'] ?? '');
        $candidature->setCvTexte($cvTexte);
        $candidature->setLmTexte($lmTexte);

        $this->em->persist($candidature);
        $this->em->flush();

        return $response->withHeader('Location', '/candidatures')->withStatus(302);
    }

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

    public function changerStatut(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $request->getAttribute('user');

        if (!$user || !$user->isAtLeastPilote()) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $id = (int)$args['id'];
        $candidature = $this->em->getRepository(Candidature::class)->find($id);

        if ($candidature) {
            $data   = $request->getParsedBody();
            $statut = $data['statut'] ?? 'en_attente';

            if (in_array($statut, ['en_attente', 'acceptee', 'refusee'])) {
                $candidature->setStatut($statut);
                $this->em->flush();
            }
        }

        return $response->withHeader('Location', '/candidatures/gestion')->withStatus(302);
    }
}