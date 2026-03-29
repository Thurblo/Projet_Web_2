<?php

namespace App\Application\Controller;

use App\Domain\Entreprise;
use DateTimeImmutable;
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
        $search = trim($queryParams['search'] ?? '');
        $page = max(1, (int)($queryParams['page'] ?? 1));

        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $countQb = $repository->createQueryBuilder('e')
            ->select('COUNT(e.id)');

        $listQb = $repository->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($perPage);

        if ($search !== '') {
            $countQb
                ->where('LOWER(e.nom) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower($search) . '%');

            $listQb
                ->where('LOWER(e.nom) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower($search) . '%');
        }

        $totalEntreprises = (int) $countQb
            ->getQuery()
            ->getSingleScalarResult();

        $totalPages = max(1, (int) ceil($totalEntreprises / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $perPage;

            $listQb->setFirstResult($offset);
        }

        $entreprises = $listQb
            ->getQuery()
            ->getResult();

        return $view->render($response, 'ENTREPRISES-Liste.html.twig', [
            'entreprises' => $entreprises,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalEntreprises' => $totalEntreprises,
            'search' => $search,
        ]);
    }

    public function ajoute(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        $success = false;
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
                    DateTimeImmutable::createFromFormat('Y-m-d', $date),
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
                $success = true;
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
            'success' => $success,
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

        $success = false;

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
                $entreprise->setDateCreation(DateTimeImmutable::createFromFormat('Y-m-d', $dateCreation));
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
                $success = true;
            }
        }

        return $view->render($response, 'ENTREPRISES-Modifier.html.twig', [
            'entreprise' => $entreprise,
            'success' => $success,
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
    
        if (!$entreprise)
        {
            return $response->withStatus(404);
        }
    
        return $view->render($response, 'ENTREPRISES-description.html.twig', ['entreprise' => $entreprise,]);
    }
}