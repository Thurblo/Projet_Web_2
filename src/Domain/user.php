<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'users')]
class User
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[Column(type: Types::STRING, length: 255, unique: true)]
    private string $email;

    #[Column(type: Types::STRING, length: 255)]
    private string $motDePasse;

    #[Column(type: Types::STRING, length: 100)]
    private string $nom;

    #[Column(type: Types::STRING, length: 100)]
    private string $prenom;

    #[Column(type: Types::STRING, enumType: Role::class)]
    private Role $role = Role::ETUDIANT;

    // Getters
    public function getId(): int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getMotDePasse(): string { return $this->motDePasse; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getRole(): Role { return $this->role; }

    // Setters
    public function setEmail(string $email): void { $this->email = $email; }
    public function setMotDePasse(string $motDePasse): void { $this->motDePasse = $motDePasse; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setRole(Role $role): void { $this->role = $role; }

    // Helpers pour Twig
    public function isAdmin(): bool { return $this->role === Role::ADMIN; }
    public function isPilote(): bool { return $this->role === Role::PILOTE; }
    public function isEtudiant(): bool { return $this->role === Role::ETUDIANT; }
    public function isAtLeastPilote(): bool { return $this->role === Role::PILOTE || $this->role === Role::ADMIN; }


    // Vérifie si l'utilisateur peut créer des comptes d'un certain rôle
    public function canCreate(Role $targetRole): bool
    {
        return match($targetRole) {
            Role::ETUDIANT => $this->isAtLeastPilote(), // Admin et Pilote peuvent créer des étudiants
            Role::PILOTE   => $this->isAdmin(),         // Seul l'Admin peut créer des pilotes
            Role::ADMIN    => false,                    // Personne ne peut créer un admin
        };
    }

    // Vérifie si l'utilisateur peut modifier un autre utilisateur
    public function canEdit(User $target): bool
    {
        // Admin peut tout modifier
        if ($this->isAdmin()) return true;

        // Pilote peut modifier uniquement les étudiants
        if ($this->isPilote() && $target->isEtudiant()) return true;

        // Un utilisateur peut modifier son propre profil
        if ($this->getId() === $target->getId()) return true;

        return false;
    }

    // Vérifie si l'utilisateur peut supprimer un autre utilisateur
    public function canDelete(User $target): bool
    {
        // Admin peut tout supprimer sauf lui-même
        if ($this->isAdmin() && $this->getId() !== $target->getId()) return true;

        // Pilote peut supprimer uniquement les étudiants
        if ($this->isPilote() && $target->isEtudiant()) return true;

        return false;
    }
}