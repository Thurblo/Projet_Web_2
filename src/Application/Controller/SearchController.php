<?php

namespace App\Application\Controller;

use App\Domain\Offre;
use App\Domain\Entreprise;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SearchController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function search(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $q = trim($request->getQueryParams()['q'] ?? '');

        if (strlen($q) < 2) {
            $response->getBody()->write(json_encode(['offres' => [], 'entreprises' => []]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        // Recherche offres
        $offres = $this->em->getRepository(Offre::class)
            ->createQueryBuilder('o')
            ->join('o.entreprise', 'e')
            ->where('o.titre LIKE :q OR o.ville LIKE :q OR o.niveau LIKE :q OR e.nom LIKE :q')
            ->setParameter('q', '%' . $q . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Recherche entreprises
        $entreprises = $this->em->getRepository(Entreprise::class)
            ->createQueryBuilder('e')
            ->where('e.nom LIKE :q OR e.ville LIKE :q OR e.type LIKE :q')
            ->setParameter('q', '%' . $q . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $result = [
            'offres' => array_map(fn($o) => [
                'id'         => $o->getId(),
                'titre'      => $o->getTitre(),
                'entreprise' => $o->getEntreprise()->getNom(),
                'ville'      => $o->getVille(),
                'duree'      => $o->getDuree(),
            ], $offres),
            'entreprises' => array_map(fn($e) => [
                'id'   => $e->getId(),
                'nom'  => $e->getNom(),
                'ville' => $e->getVille(),
                'type' => $e->getType(),
            ], $entreprises),
        ];

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }
}