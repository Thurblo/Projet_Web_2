<?php

namespace App\Application\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class HomeController
{
    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        $offres = [
            [
                'id' => 1,
                'badge' => 'Urgent',
                'badgeClass' => 'badge-urgent',
                'titre' => 'Développeur Web Full Stack',
                'entreprise' => 'TechCorp',
                'entrepriseDetail' => 'TechCorp ↗',
                'lieuCourt' => '🌐 Télétravail',
                'lieu' => 'Télétravail',
                'salaire' => '800 €/mois',
                'tags' => ['Temps plein', 'React'],
                'candidature' => 'Candidature simplifiée',
                'annonceType' => 'Annonce',
                'typeLine' => 'Temps plein',
                'contrat' => 'Temps plein',
                'horaires' => 'Flextime',
                'response' => 'A répondu à 90 % des candidatures sur les 30 derniers jours, généralement en 1 jour.',
                'avantages' => ['Télétravail complet', 'Flextime', 'Intéressement et participation'],
                'description' => "Vous rejoindrez une équipe dynamique pour développer et maintenir des applications web modernes. Vous interviendrez sur l'ensemble de la stack technique, du backend API au frontend responsive.",
                'competences' => ['PHP', 'Symfony', 'JavaScript', 'React', 'SQL', 'Git']
            ],
            [
                'id' => 2,
                'badge' => 'Répond souvent dans un délai de 5 jours',
                'badgeClass' => 'badge-delay',
                'titre' => 'Intégrateur HTML/CSS',
                'entreprise' => 'AgenceWeb',
                'entrepriseDetail' => 'AgenceWeb ↗',
                'lieuCourt' => '📍 15 min · Lyon (69)',
                'lieu' => 'Lyon (69)',
                'salaire' => 'Gratification légale',
                'tags' => ['Temps partiel', 'Figma'],
                'candidature' => 'Candidature simplifiée',
                'annonceType' => 'Annonce',
                'typeLine' => 'Temps partiel',
                'contrat' => 'Temps partiel',
                'horaires' => 'Horaires flexibles',
                'response' => 'Répond souvent dans un délai de 5 jours.',
                'avantages' => ['Flextime', 'Formation continue', 'Tickets restaurant'],
                'description' => 'Au sein de notre agence créative, vous intégrerez des maquettes Figma en pages web responsive. Vous travaillerez en binôme avec un développeur senior.',
                'competences' => ['HTML', 'CSS', 'Bootstrap', 'Figma', 'Git']
            ],
            [
                'id' => 3,
                'badge' => 'Répond souvent dans un délai de 11 jours',
                'badgeClass' => 'badge-delay',
                'titre' => 'Développeur PHP/Symfony',
                'entreprise' => 'StartupIO',
                'entrepriseDetail' => 'StartupIO ↗',
                'lieuCourt' => '📍 Bordeaux (33)',
                'lieu' => 'Bordeaux (33)',
                'salaire' => '1 200 €/mois',
                'tags' => ['Temps plein', '+1'],
                'candidature' => 'Candidature simplifiée',
                'annonceType' => 'Annonce',
                'typeLine' => 'Temps plein',
                'contrat' => 'Temps plein',
                'horaires' => 'Flextime',
                'response' => 'Répond souvent dans un délai de 11 jours.',
                'avantages' => ['Intéressement', 'Participation', "Mutuelle d'entreprise"],
                'description' => "Intégrez notre startup en pleine croissance et participez au développement de notre plateforme SaaS. Environnement agile, beaucoup d'autonomie et de montée en compétences.",
                'competences' => ['PHP', 'Symfony', 'Twig', 'MySQL', 'Docker', 'API REST']
            ]
        ];

        $offreSelectionnee = $offres[0];

        return $view->render($response, 'home.html.twig', [
            'offres' => $offres,
            'offreSelectionnee' => $offreSelectionnee
        ]);
    }

    public function connexion(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
    
        return $view->render($response, 'Connexion.html.twig', [
            'name' => 'John',
        ]);
    }
}