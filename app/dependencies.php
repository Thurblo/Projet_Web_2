<?php

declare(strict_types=1);

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
<<<<<<< HEAD
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
=======
>>>>>>> 317656a1c341b4d8e9fd61b7c1b8b0ebdbc1a296

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
<<<<<<< HEAD
        EntityManager::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $doctrine = $settings->get('doctrine');

            // Use the ArrayAdapter or the FilesystemAdapter depending on the value of the 'dev_mode' setting
            // You can substitute the FilesystemAdapter for any other cache you prefer from the symfony/cache library
            $cache = $doctrine['dev_mode'] ?
                new ArrayAdapter() :
                new FilesystemAdapter(directory: $doctrine['cache_dir']);

            $config = ORMSetup::createAttributeMetadataConfiguration(
                $doctrine['metadata_dirs'],
                $doctrine['dev_mode'],
                null,
                $cache
            );

            $connection = DriverManager::getConnection($doctrine['connection']);

            return new EntityManager($connection, $config);
        },

=======
>>>>>>> 317656a1c341b4d8e9fd61b7c1b8b0ebdbc1a296
    ]);
};
