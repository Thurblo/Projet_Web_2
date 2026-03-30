<?php

namespace App\Application\Controller;

use App\Domain\Offre;
use App\Domain\Entreprise;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class OffresController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $repository = $this->em->getRepository(Offre::class);

        $queryParams = $request->getQueryParams();
        $search = trim($queryParams['search'] ?? '');
        $page   = max(1, (int)($queryParams['page'] ?? 1));
        $perPage = 5;
        $offset  = ($page - 1) * $perPage;

        $countQb = $repository->createQueryBuilder('o')->select('COUNT(o.id)');
        $listQb  = $repository->createQueryBuilder('o')
            ->join('o.entreprise', 'e')
            ->addSelect('e')
            ->orderBy('o.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($perPage);

        if ($search !== '') {
            $countQb->where('LOWER(o.titre) LIKE :search')->setParameter('search', '%' . mb_strtolower($search) . '%');
            $listQb->where('LOWER(o.titre) LIKE :search')->setParameter('search', '%' . mb_strtolower($search) . '%');
        }

        $totalOffres = (int) $countQb->getQuery()->getSingleScalarResult();
        $totalPages  = max(1, (int) ceil($totalOffres / $perPage));

        if ($page > $totalPages) {
            $page   = $totalPages;
            $offset = ($page - 1) * $perPage;
            $listQb->setFirstResult($offset);
        }

        $offres = $listQb->getQuery()->getResult();

        return $view->render($response, 'OFFRES-Liste.html.twig', [
            'offres'       => $offres,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalOffres'  => $totalOffres,
            'search'       => $search,
        ]);
    }


    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $entreprises = $this->em->getRepository(Entreprise::class)->findBy([], ['nom' => 'ASC']);
        $success = false;
        $errors  = [];

        if ($request->getMethod() === 'POST') {
            $b = $request->getParsedBody();

            $entrepriseId = (int)($b['entreprise_id'] ?? 0);
            $entreprise   = $this->em->find(Entreprise::class, $entrepriseId);
            $titre        = trim($b['titre'] ?? '');
            $telephone    = trim($b['telephone'] ?? '');
            $dateDebut    = trim($b['date'] ?? date('Y-m-d'));
            $duree        = (int)($b['duree'] ?? 0);
            $ville        = trim($b['ville'] ?? '');
            $remuneration = (int)($b['remuneration'] ?? 0);
            $description  = trim($b['description'] ?? '');
            $missions     = trim($b['missions'] ?? '');
            $niveau       = trim($b['niveau'] ?? '');
            $competences  = trim($b['competences'] ?? '');
            $email        = trim($b['email'] ?? '');

            if (!$entreprise)   { $errors[] = 'Veuillez sélectionner une entreprise.'; }
            if ($titre === '')  { $errors[] = 'Le titre est obligatoire.'; }

            if (empty($errors)) {
                $offre = new Offre(
                    $entreprise,
                    $titre,
                    $telephone,
                    DateTimeImmutable::createFromFormat('Y-m-d', $dateDebut),
                    $duree,
                    $ville,
                    $remuneration,
                    $description,
                    $missions,
                    $niveau,
                    $competences,
                    $email,
                );
                $this->em->persist($offre);
                $this->em->flush();
                $success = true;
            }
        }

        return $view->render($response, 'OFFRES-Crée.html.twig', [
            'entreprises' => $entreprises,
            'success'     => $success,
            'errors'      => $errors,
        ]);
    }

    public function modify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $entreprises = $this->em->getRepository(Entreprise::class)->findBy([], ['nom' => 'ASC']);

        $id    = (int)($args['id'] ?? 0);
        $offre = $this->em->find(Offre::class, $id);

        if (!$offre) {
            return $response->withStatus(404);
        }

        $success = false;
        $errors  = [];

        if ($request->getMethod() === 'POST') {
            $b = $request->getParsedBody();

            $entrepriseId = (int)($b['entreprise_id'] ?? 0);
            $entreprise   = $this->em->find(Entreprise::class, $entrepriseId);
            $titre        = trim($b['titre'] ?? '');
            $telephone    = trim($b['telephone'] ?? '');
            $dateDebut    = trim($b['date'] ?? date('Y-m-d'));
            $duree        = (int)($b['duree'] ?? 0);
            $ville        = trim($b['ville'] ?? '');
            $remuneration = (int)($b['remuneration'] ?? 0);
            $description  = trim($b['description'] ?? '');
            $missions     = trim($b['missions'] ?? '');
            $niveau       = trim($b['niveau'] ?? '');
            $competences  = trim($b['competences'] ?? '');
            $email        = trim($b['email'] ?? '');

            if (!$entreprise)   { $errors[] = 'Veuillez sélectionner une entreprise.'; }
            if ($titre === '')  { $errors[] = 'Le titre est obligatoire.'; }

            if (empty($errors)) {
                $offre->setEntreprise($entreprise);
                $offre->setTitre($titre);
                $offre->setTelephone($telephone);
                $offre->setDateDebut(DateTimeImmutable::createFromFormat('Y-m-d', $dateDebut));
                $offre->setDuree($duree);
                $offre->setVille($ville);
                $offre->setRemuneration($remuneration);
                $offre->setDescription($description);
                $offre->setMissions($missions);
                $offre->setNiveau($niveau);
                $offre->setCompetences($competences);
                $offre->setEmail($email);
                $this->em->flush();
                $success = true;
            }
        }

        return $view->render($response, 'OFFRES-Modifier.html.twig', [
            'offre'       => $offre,
            'entreprises' => $entreprises,
            'success'     => $success,
            'errors'      => $errors,
        ]);
    }

    public function supprimer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id    = (int)$args['id'];
        $offre = $this->em->find(Offre::class, $id);

        if ($offre) {
            $this->em->remove($offre);
            $this->em->flush();
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('offres');

        return $response->withHeader('Location', $url)->withStatus(302);
    }

    public function description(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $id = (int)($args['id'] ?? 0);
        $offre = $this->em->find(Offre::class, $id);

        if (!$offre) {
            return $response->withStatus(404);
        }

        return $view->render($response, 'OFFRES-description.html.twig', [
            'offre' => $offre,
        ]);
    }
}