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
use App\Application\Controller\CampusController;
use App\Application\Middleware\LoggedMiddleware;
use App\Application\Middleware\RoleCheckMiddleware;
use App\Domain\Role;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Routing\RouteCollectorProxy;
use App\Application\Controller\LoginController;
use App\Application\Controller\CandidatureController;
use App\Application\Controller\SearchController;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $factory = $app->getContainer()->get(ResponseFactoryInterface::class);

    // ── PUBLIQUES ──
    $app->get('/', [HomeController::class, 'home']);
    $app->get('/home', [HomeController::class, 'home'])->setName('home');
    $app->get('/home/connexion', [HomeController::class, 'connexion'])->setName('home.connexion');
    $app->get('/mentions', [HomeController::class, 'mention'])->setName('mentions');
    $app->get('/Login', [LoginController::class, 'login'])->setName('login');
    $app->post('/Login', [LoginController::class, 'login']);
    $app->get('/logout', [HomeController::class, 'logout'])->setName('logout');

    // ── CONNECTÉS ──
    $app->get('/profile', [ProfileController::class, 'index'])->setName('profile')
        ->add(new LoggedMiddleware($factory));
    $app->post('/profile', [ProfileController::class, 'edit'])->setName('profile.edit')
        ->add(new LoggedMiddleware($factory));

    $app->get('/search', [SearchController::class, 'search'])->setName('search')
        ->add(new LoggedMiddleware($factory));

    $app->get('/wishlist', [WishlistController::class, 'index'])->setName('wishlist')
        ->add(new LoggedMiddleware($factory));
    $app->get('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->setName('wishlist.toggle')
        ->add(new LoggedMiddleware($factory));

    $app->get('/entreprises[/{page:\d+}]', [EntreprisesController::class, 'index'])->setName('entreprises')
        ->add(new LoggedMiddleware($factory));
    $app->get('/entreprises/creer', [EntreprisesController::class, 'ajoute'])->setName('entreprises.creer')
        ->add(new LoggedMiddleware($factory));
    $app->post('/entreprises/creer', [EntreprisesController::class, 'ajoute'])
        ->add(new LoggedMiddleware($factory));
    $app->get('/entreprises/modifier/{id:\d+}', [EntreprisesController::class, 'modifier'])->setName('entreprises.modifier')
        ->add(new LoggedMiddleware($factory));
    $app->post('/entreprises/modifier/{id:\d+}', [EntreprisesController::class, 'modifier'])
        ->add(new LoggedMiddleware($factory));
    $app->post('/entreprises/supprimer/{id:\d+}', [EntreprisesController::class, 'supprimer'])->setName('entreprises.supprimer')
        ->add(new LoggedMiddleware($factory));
    $app->get('/entreprises/description/{id:\d+}', [EntreprisesController::class, 'description'])->setName('entreprises.description')
        ->add(new LoggedMiddleware($factory));

    $app->get('/offres[/{page:\d+}]', [OffresController::class, 'index'])->setName('offres')
        ->add(new LoggedMiddleware($factory));
    $app->get('/offres/creer', [OffresController::class, 'ajoute'])->setName('offres.creer')
        ->add(new LoggedMiddleware($factory));
    $app->post('/offres/creer', [OffresController::class, 'ajoute'])
        ->add(new LoggedMiddleware($factory));
    $app->get('/offres/modifier/{id:\d+}', [OffresController::class, 'modifier'])->setName('offres.modifier')
        ->add(new LoggedMiddleware($factory));
    $app->post('/offres/modifier/{id:\d+}', [OffresController::class, 'modifier'])
        ->add(new LoggedMiddleware($factory));
    $app->post('/offres/supprimer/{id:\d+}', [OffresController::class, 'supprimer'])->setName('offres.supprimer')
        ->add(new LoggedMiddleware($factory));
    $app->get('/offres/description/{id:\d+}', [OffresController::class, 'description'])->setName('offres.description')
        ->add(new LoggedMiddleware($factory));

    $app->get('/compte', [CompteController::class, 'index'])->setName('compte')
        ->add(new LoggedMiddleware($factory));
    $app->post('/compte', [CompteController::class, 'create'])->setName('compte.creer')
        ->add(new LoggedMiddleware($factory));

    // ── CANDIDATURES ──
    $app->get('/candidatures', [CandidatureController::class, 'mesCandidatures'])->setName('candidatures')
        ->add(new LoggedMiddleware($factory));
    $app->get('/candidatures/postuler/{id:\d+}', [CandidatureController::class, 'postuler'])->setName('candidatures.postuler.form')
        ->add(new LoggedMiddleware($factory));
    $app->post('/candidatures/postuler/{id:\d+}', [CandidatureController::class, 'postuler'])->setName('candidatures.postuler')
        ->add(new LoggedMiddleware($factory));
    $app->post('/candidatures/annuler/{id:\d+}', [CandidatureController::class, 'annuler'])->setName('candidatures.annuler')
        ->add(new LoggedMiddleware($factory));
    $app->get('/candidatures/gestion', [CandidatureController::class, 'gestion'])->setName('candidatures.gestion')
        ->add(new LoggedMiddleware($factory));
    $app->post('/candidatures/statut/{id:\d+}', [CandidatureController::class, 'changerStatut'])->setName('candidatures.statut')
        ->add(new LoggedMiddleware($factory));

    // ── CAMPUS ──
    $app->group('/campus', function (RouteCollectorProxy $group) {
        $group->get('/liste', [CampusController::class, 'index'])->setName('campus.liste');
        $group->get('/creer', [CampusController::class, 'creer'])->setName('campus.creer');
        $group->post('/creer', [CampusController::class, 'creer']);
        $group->get('/voir/{id:\d+}', [CampusController::class, 'voir'])->setName('campus.voir');
        $group->get('/modifier/{id:\d+}', [CampusController::class, 'modifier'])->setName('campus.modifier');
        $group->post('/modifier/{id:\d+}', [CampusController::class, 'modifier']);
        $group->post('/supprimer/{id:\d+}', [CampusController::class, 'supprimer'])->setName('campus.supprimer');
        $group->get('/rattacher/{id:\d+}', [CampusController::class, 'rattacher'])->setName('campus.rattacher');
        $group->post('/rattacher/{id:\d+}', [CampusController::class, 'rattacher']);
    })->add(new RoleCheckMiddleware($factory, [Role::PILOTE, Role::ADMIN]));

    // ── ETUDIANTS ──
    $app->group('/etudiant', function (RouteCollectorProxy $group) {
        $group->get('/liste', [EtudiantController::class, 'index'])->setName('liste_etudiants');
        $group->get('/voir/{id:\d+}', [EtudiantController::class, 'voir'])->setName('etudiant.voir');
        $group->get('/modifier/{id:\d+}', [EtudiantController::class, 'modify'])->setName('etudiants.modifier');
        $group->post('/modifier/{id:\d+}', [EtudiantController::class, 'modify']);
        $group->post('/supprimer/{id:\d+}', [EtudiantController::class, 'supprimer'])->setName('etudiants.supprimer');
    })->add(new RoleCheckMiddleware($factory, [Role::PILOTE, Role::ADMIN]));

    // ── PILOTES ──
    $app->group('/pilote', function (RouteCollectorProxy $group) {
        $group->get('/liste', [PiloteController::class, 'index'])->setName('liste_pilotes');
        $group->get('/voir/{id:\d+}', [PiloteController::class, 'voir'])->setName('pilote.voir');
        $group->get('/modifier/{id:\d+}', [PiloteController::class, 'modify'])->setName('pilotes.modifier');
        $group->post('/modifier/{id:\d+}', [PiloteController::class, 'modify']);
        $group->post('/supprimer/{id:\d+}', [PiloteController::class, 'supprimer'])->setName('pilotes.supprimer');
    })->add(new RoleCheckMiddleware($factory, [Role::ADMIN]));



};