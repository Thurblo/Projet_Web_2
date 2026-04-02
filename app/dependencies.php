<?php

declare(strict_types=1);

use App\Application\Controller\LoginController;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use App\Application\Controller\WishlistController;
use App\Application\Controller\CompteController;
use App\Application\Controller\ProfileController;
use App\Application\Controller\EtudiantController;
use App\Application\Controller\PiloteController;


return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        EntityManager::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $doctrine = $settings->get('doctrine');


            $cache = $doctrine['dev_mode'] ?
                new ArrayAdapter() :
                new FilesystemAdapter(directory: $doctrine['cache_dir']);

            $config = ORMSetup::createAttributeMetadataConfiguration(
                $doctrine['metadata_dirs'],
                $doctrine['dev_mode'],
                null,
                $cache
            );

            $connection = DriverManager::getConnection($doctrine['connection'])
            ;
            return new EntityManager($connection, $config);
        },

        ResponseFactoryInterface::class => function (ContainerInterface $container) {
            return $container->get(ResponseFactory::class);
        },

        // ↓ Ajout du LoginController
        LoginController::class => function (ContainerInterface $c) {
            return new LoginController($c->get(EntityManager::class));
        },

        CompteController::class => function (ContainerInterface $c) {
            return new CompteController($c->get(EntityManager::class));
        },

        ProfileController::class => function (ContainerInterface $c) {
            return new ProfileController($c->get(EntityManager::class));
        },

        EtudiantController::class => function (ContainerInterface $c) {
            return new EtudiantController($c->get(EntityManager::class));
        },

        PiloteController::class => function (ContainerInterface $c) {
            return new PiloteController($c->get(EntityManager::class));
        },

        WishlistController::class => function (ContainerInterface $c) {
            return new WishlistController($c->get(EntityManager::class));
        },

        CandidatureController::class => function (ContainerInterface $c) {
            return new CandidatureController($c->get(EntityManager::class));
        },

        CampusController::class => function (ContainerInterface $c) {
            return new CampusController($c->get(EntityManager::class));
        },

    ]);
};
