<?php

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'campus')]
class Campus
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', nullable: false)]
    private string $ville;

    #[Column(type: 'string', nullable: true)]
    private ?string $nom = null;

    #[OneToMany(mappedBy: 'campus', targetEntity: User::class)]
    private Collection $etudiants;

    public function __construct(string $ville, ?string $nom = null)
    {
        $this->ville = $ville;
        $this->nom = $nom;
        $this->etudiants = new ArrayCollection();
    }

    public function getId(): int { return $this->id; }

    public function getVille(): string { return $this->ville; }
    public function setVille(string $ville): void { $this->ville = $ville; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): void { $this->nom = $nom; }

    public function getEtudiants(): Collection { return $this->etudiants; }
}