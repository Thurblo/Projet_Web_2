<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\User;
use App\Domain\Role;

$containerBuilder = new \DI\ContainerBuilder();

// Charge les settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

$container = $containerBuilder->build();

$em = $container->get(\Doctrine\ORM\EntityManager::class);

$user = new User();
$user->setNom('Admin');
$user->setPrenom('Admin');
$user->setEmail('admin@admin.com');
$user->setMotDePasse(password_hash('admin123', PASSWORD_DEFAULT));
$user->setRole(Role::ADMIN);

$em->persist($user);
$em->flush();

echo "Compte admin créé avec succès !\n";
echo "Email : admin@admin.com\n";
echo "Mot de passe : admin123\n";