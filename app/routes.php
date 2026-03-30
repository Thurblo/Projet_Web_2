<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\Application\Controller\HomeController;
use App\Application\Controller\WishlistController;
use App\Application\Controller\OffresController;
use App\Application\Controller\ProfileController;
use App\Application\Controller\EtudiantController;
use App\Application\Controller\EntreprisesController;
use App\Application\Controller\CompteController;
use App\Application\Controller\PiloteController;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', [HomeController::class, 'home']);

    $app->get('/home', [HomeController::class, 'home'])
    ->setName('home');

    $app->get('/wishlist', [WishlistController::class, 'index'])
    ->setName('wishlist');

    $app->get('/offres', [OffresController::class, 'index'])
    ->setName('offres');

    $app->get('/offres/creer', [OffresController::class, 'create'])
    ->setName('offres.creer');

    $app->get('/offres/modifier', [OffresController::class, 'modify'])
    ->setName('offres.modifier');

    $app->get('/profile', [ProfileController::class, 'index'])
    ->setName('profile');

    $app->get('/home/connexion', [HomeController::class, 'connexion'])
    ->setName('home.connexion');

    $app->get('/mentions', [HomeController::class, 'mention'])
    ->setName('mentions');

    $app->get('/entreprises[/{page:\d+}]', [EntreprisesController::class, 'index'])
    ->setName('entreprises');
    
    $app->get('/entreprises/creer', [EntreprisesController::class, 'ajoute'])
    ->setName('entreprises.creer');
    
    $app->post('/entreprises/creer', [EntreprisesController::class, 'ajoute']);
    
    $app->get('/entreprises/modifier/{id:\d+}', [EntreprisesController::class, 'modifier'])
    ->setName('entreprises.modifier');
    
    $app->post('/entreprises/modifier/{id:\d+}', [EntreprisesController::class, 'modifier']);
    
    $app->post('/entreprises/supprimer/{id:\d+}', [EntreprisesController::class, 'supprimer'])
    ->setName('entreprises.supprimer');

    $app->get('/entreprises/description/{id:\d+}', [EntreprisesController::class, 'description'])
    ->setName('entreprises.description');

   $app->get('/compte', [CompteController::class, 'index'])
        ->setName('compte');

    $app->post('/compte', [CompteController::class, 'create'])
        ->setName('compte.creer');

        $app->get('/etudiant/compte', [EtudiantController::class, 'create'])
    ->setName('etudiants.compte');

  $app->get('/etudiants[/{page:\d+}]', [EtudiantController::class, 'index'])
    ->setName('etudiants');

$app->get('/etudiant/modifier/{id:\d+}', [EtudiantController::class, 'modify'])
    ->setName('etudiants.modifier');

$app->post('/etudiant/modifier/{id:\d+}', [EtudiantController::class, 'modify']);


};