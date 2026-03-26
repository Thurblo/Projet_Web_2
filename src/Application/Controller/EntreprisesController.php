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
        $entreprises = $repository->findAll();

        return $view->render($response, 'ENTREPRISES-Liste.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        
        if ($request->getMethod() === 'POST') {
            $parsedBody = $request->getParsedBody();
            
            $entreprise = new Entreprise(
                $parsedBody['entreprise'] ?? '',      // nom
                $parsedBody['phone'] ?? '',            // téléphone
                DateTimeImmutable::createFromFormat('Y-m-d', $parsedBody['Date'] ?? date('Y-m-d')), // date
                $parsedBody['Type'] ?? '',             // type
                $parsedBody['Ville'] ?? '',            // ville
                (int)($parsedBody['Salariés'] ?? 0),   // salaries
                $parsedBody['description'] ?? '',      // description
                $parsedBody['niveau'] ?? '',           // missions
                $parsedBody['compétences'] ?? '',      // domainesExpertise
                $parsedBody['niveau'] ?? '',           // evaluation
                $parsedBody['email'] ?? ''             // email
            );
            
            $this->em->persist($entreprise);
            $this->em->flush();
            
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url = $routeParser->urlFor('entreprises');
            return $response->withHeader('Location', $url)->withStatus(302);
        }
        
        return $view->render($response, 'ENTREPRISES-Crée.html.twig');
    }

       // MODIFIER - AJOUTE CETTE METHODE
    public function modify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        
        $id = (int)($args['id'] ?? 0);
        $entreprise = $this->em->find(Entreprise::class, $id);
        
        if (!$entreprise) {
            return $response->withStatus(404);
        }
        
        if ($request->getMethod() === 'POST') {
            $parsedBody = $request->getParsedBody();
            
            $entreprise->setNom($parsedBody['entreprise'] ?? '');
            $entreprise->setTelephone($parsedBody['phone'] ?? '');
            $entreprise->setDateCreation(DateTimeImmutable::createFromFormat('Y-m-d', $parsedBody['date'] ?? date('Y-m-d')));
            $entreprise->setType($parsedBody['type'] ?? '');
            $entreprise->setVille($parsedBody['ville'] ?? '');
            $entreprise->setSalaries((int)($parsedBody['salariés'] ?? 0));
            $entreprise->setDescription($parsedBody['description'] ?? '');
            $entreprise->setMissions($parsedBody['missions'] ?? '');
            $entreprise->setDomainesExpertise($parsedBody['domaines'] ?? '');
            $entreprise->setEvaluation($parsedBody['evaluation'] ?? '');
            $entreprise->setEmail($parsedBody['email'] ?? '');
            
            $this->em->flush();
            
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url = $routeParser->urlFor('entreprises');
            return $response->withHeader('Location', $url)->withStatus(302);
        }
        
        return $view->render($response, 'ENTREPRISES-Modifier.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }
    
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)($args['id'] ?? 0);
        $entreprise = $this->em->find(Entreprise::class, $id);
        
        if ($entreprise) {
            $this->em->remove($entreprise);
            $this->em->flush();
        }
        
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('entreprises');
        
        return $response->withHeader('Location', $url)->withStatus(302);
    }
}