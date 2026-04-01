<?php

namespace App\Application\Controller;

use App\Domain\Offre;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class HomeController
{
    public function __construct(private EntityManager $em)
    {
    }

    public function home(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        $queryParams = $request->getQueryParams();
        $offreId = isset($queryParams['offre']) ? (int) $queryParams['offre'] : null;

        $repo = $this->em->getRepository(Offre::class);

        // On récupère les dernières offres ajoutées
        $offresEntities = $repo->createQueryBuilder('o')
            ->join('o.entreprise', 'e')
            ->addSelect('e')
            ->orderBy('o.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $offres = array_map(function (Offre $offre) {
            $competences = $offre->getCompetences()
                ? array_filter(array_map('trim', preg_split('/[,;]+/', $offre->getCompetences())))
                : [];

            $missions = $offre->getMissions()
                ? array_filter(array_map('trim', preg_split('/[\r\n,;]+/', $offre->getMissions())))
                : [];

            return [
                'id' => $offre->getId(),
                'titre' => $offre->getTitre(),
                'entreprise' => $offre->getEntreprise()->getNom(),
                'telephone' => $offre->getTelephone(),
                'dateDebut' => $offre->getDateDebut(),
                'duree' => $offre->getDuree(),
                'ville' => $offre->getVille(),
                'remuneration' => $offre->getRemuneration(),
                'description' => $offre->getDescription(),
                'niveau' => $offre->getNiveau(),
                'email' => $offre->getEmail(),
                'competencesListe' => $competences,
                'missionsListe' => $missions,
            ];
        }, $offresEntities);

        $offreSelectionnee = null;

        if ($offreId !== null) {
            foreach ($offres as $offre) {
                if ($offre['id'] === $offreId) {
                    $offreSelectionnee = $offre;
                    break;
                }
            }

            if ($offreSelectionnee === null) {
                $selectedEntity = $repo->createQueryBuilder('o')
                    ->join('o.entreprise', 'e')
                    ->addSelect('e')
                    ->where('o.id = :id')
                    ->setParameter('id', $offreId)
                    ->getQuery()
                    ->getOneOrNullResult();

                if ($selectedEntity) {
                    $competences = $selectedEntity->getCompetences()
                        ? array_filter(array_map('trim', preg_split('/[,;]+/', $selectedEntity->getCompetences())))
                        : [];

                    $missions = $selectedEntity->getMissions()
                        ? array_filter(array_map('trim', preg_split('/[\r\n,;]+/', $selectedEntity->getMissions())))
                        : [];

                    $offreSelectionnee = [
                        'id' => $selectedEntity->getId(),
                        'titre' => $selectedEntity->getTitre(),
                        'entreprise' => $selectedEntity->getEntreprise()->getNom(),
                        'telephone' => $selectedEntity->getTelephone(),
                        'dateDebut' => $selectedEntity->getDateDebut(),
                        'duree' => $selectedEntity->getDuree(),
                        'ville' => $selectedEntity->getVille(),
                        'remuneration' => $selectedEntity->getRemuneration(),
                        'description' => $selectedEntity->getDescription(),
                        'niveau' => $selectedEntity->getNiveau(),
                        'email' => $selectedEntity->getEmail(),
                        'competencesListe' => $competences,
                        'missionsListe' => $missions,
                    ];
                }
            }
        }

        if ($offreSelectionnee === null) {
            $offreSelectionnee = $offres[0] ?? null;
        }

        return $view->render($response, 'home.html.twig', [
            'offres' => $offres,
            'offreSelectionnee' => $offreSelectionnee,
        ]);
    }

    public function connexion(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'Connexion.html.twig', [
            'name' => 'John',
        ]);
    }

    public function mention(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
    
        return $view->render($response, '/mentions.html.twig', [
            'name' => 'John',
        ]);
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Vider complètement la session
        $_SESSION = [];

        // Supprimer le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        return $response->withHeader('Location', '/Login')->withStatus(302);
    }
}