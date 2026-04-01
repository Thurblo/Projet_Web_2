<?php

namespace App\Application\Controller;

use App\Domain\Wishlist;
use App\Domain\Offre;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class WishlistController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $user = $request->getAttribute('user');

        if (!$user) {
            return $response->withHeader('Location', '/Login')->withStatus(302);
        }

        $wishlists = $this->em->getRepository(Wishlist::class)
            ->createQueryBuilder('w')
            ->join('w.offre', 'o')
            ->addSelect('o')
            ->where('w.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $offres = array_map(fn($w) => $w->getOffre(), $wishlists);

        return $view->render($response, 'WISHLIST.html.twig', [
            'offres' => $offres
        ]);
    }

    public function toggle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $request->getAttribute('user');

        if (!$user) {
            return $response->withHeader('Location', '/Login')->withStatus(302);
        }

        $offreId = (int)($args['id'] ?? 0);
        $offre = $this->em->find(Offre::class, $offreId);

        if (!$offre) {
            return $response->withStatus(404);
        }

        $existingWishlist = $this->em->getRepository(Wishlist::class)
            ->findOneBy(['user' => $user, 'offre' => $offre]);

        if ($existingWishlist) {
            $this->em->remove($existingWishlist);
        } else {
            $wishlist = new Wishlist($user, $offre);
            $this->em->persist($wishlist);
        }

        $this->em->flush();

        $referer = $request->getHeaderLine('Referer');
        if ($referer) {
            return $response->withHeader('Location', $referer)->withStatus(302);
        }

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}