<?php

namespace App\Application\Controller;

use App\Domain\Entreprise;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

class EntreprisesController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        
        $repository = $this->em->getRepository(Entreprise::class);
        
        $queryParams = $request->getQueryParams();
        $searchTerm = isset($queryParams['search']) ? trim($queryParams['search']) : '';
        
        $perPage = 5;
        $page = isset($args['page']) ? (int)$args['page'] : 1;
        $offset = ($page - 1) * $perPage;
        
        $qb = $repository->createQueryBuilder('e');
        
        if ($searchTerm !== '') {
            $qb->where('e.nom LIKE :search')
               ->setParameter('search', '%' . $searchTerm . '%');
        }
        
        $countQb = clone $qb;
        $totalEntreprises = $countQb->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
        
        $entreprises = $qb->orderBy('e.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();
        
        $totalPages = (int)ceil($totalEntreprises / $perPage);
        
        return $view->render($response, 'ENTREPRISES-Liste.html.twig', [
            'entreprises' => $entreprises,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalEntreprises' => $totalEntreprises,
            'searchTerm' => $searchTerm,
        ]);
    }

    public function ajoute(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        $nom = '';
        $phone = '';
        $date = '';
        $type = '';
        $ville = '';
        $salaries = '';
        $description = '';
        $missions = '';
        $domaines = '';
        $evaluation = '';
        $email = '';
        $statut = '';

        if ($request->getMethod() === 'POST') {
            $parsedBody = $request->getParsedBody();
            $nom = trim($parsedBody['nom'] ?? '');
            $phone = trim($parsedBody['phone'] ?? '');
            $date = trim($parsedBody['date'] ?? date('Y-m-d'));
            $type = trim($parsedBody['type'] ?? '');
            $ville = trim($parsedBody['ville'] ?? '');
            $salaries = trim($parsedBody['salaries'] ?? '');
            $description = trim($parsedBody['description'] ?? '');
            $missions = trim($parsedBody['missions'] ?? '');
            $domaines = trim($parsedBody['domaines'] ?? '');
            $evaluation = trim($parsedBody['evaluation'] ?? '');
            $email = trim($parsedBody['email'] ?? '');
            $statut = trim($parsedBody['statut'] ?? '');

            if ($nom !== '' && $phone !== '') {
                $nouvelleEntreprise = new Entreprise(
                    $nom,
                    $phone,
                    $date,
                    $type,
                    $ville,
                    $salaries,
                    $description,
                    $missions,
                    $domaines,
                    $evaluation,
                    $email,
                    $statut
                );
                $this->em->persist($nouvelleEntreprise);
                $this->em->flush();

                return $response->withHeader('Location', '/entreprises')->withStatus(302);
            }
        }

        return $view->render($response, 'ENTREPRISES-Crée.html.twig', [
            'nom' => $nom,
            'phone' => $phone,
            'date' => $date,
            'type' => $type,
            'ville' => $ville,
            'salaries' => $salaries,
            'description' => $description,
            'missions' => $missions,
            'domaines' => $domaines,
            'evaluation' => $evaluation,
            'email' => $email,
            'statut' => $statut,
        ]);
    }

    public function modifier(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        $id = (int)($args['id'] ?? 0);
        $entreprise = $this->em->find(Entreprise::class, $id);

        if (!$entreprise) {
            return $response->withStatus(404);
        }

        if ($request->getMethod() === 'POST') {
            $parsedBody = $request->getParsedBody();
            $nom = trim($parsedBody['nom'] ?? '');
            $telephone = trim($parsedBody['phone'] ?? '');
            $dateCreation = trim($parsedBody['date'] ?? date('Y-m-d'));
            $type = trim($parsedBody['type'] ?? '');
            $ville = trim($parsedBody['ville'] ?? '');
            $salaries = trim($parsedBody['salaries'] ?? '');
            $desc = trim($parsedBody['description'] ?? '');
            $missions = trim($parsedBody['missions'] ?? '');
            $domaines = trim($parsedBody['domaines'] ?? '');
            $evaluation = trim($parsedBody['evaluation'] ?? '');
            $email = trim($parsedBody['email'] ?? '');
            $statut = trim($parsedBody['statut'] ?? '');

            if ($nom !== '' && $telephone !== '') {
                $entreprise->setNom($nom);
                $entreprise->setTelephone($telephone);
                $entreprise->setDateCreation($dateCreation);
                $entreprise->setType($type);
                $entreprise->setVille($ville);
                $entreprise->setSalaries($salaries);
                $entreprise->setDescription($desc);
                $entreprise->setMissions($missions);
                $entreprise->setDomainesExpertise($domaines);
                $entreprise->setEvaluation($evaluation);
                $entreprise->setEmail($email);
                $entreprise->setStatut($statut);
                $this->em->flush();

                return $response->withHeader('Location', '/entreprises')->withStatus(302);
            }
        }

        return $view->render($response, 'ENTREPRISES-Modifier.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    public function supprimer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)$args['id'];
        $entreprise = $this->em->find(Entreprise::class, $id);

        if ($entreprise) {
            $this->em->remove($entreprise);
            $this->em->flush();
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('entreprises');

        return $response->withHeader('Location', $url)->withStatus(302);
    }

    public function description(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        $id = (int)$args['id'];
        $entreprise = $this->em->find(Entreprise::class, $id);

        if (!$entreprise) {
            return $response->withStatus(404);
        }

        $user = $request->getAttribute('user');
        $wishlistIds = [];

        if ($user) {
            $wishlists = $this->em->getRepository(\App\Domain\Wishlist::class)
                ->findBy(['user' => $user]);
            $wishlistIds = array_map(fn($w) => $w->getOffre()->getId(), $wishlists);
        }

        return $view->render($response, 'ENTREPRISES-description.html.twig', [
            'entreprise'  => $entreprise,
            'wishlistIds' => $wishlistIds,
        ]);
    }
}