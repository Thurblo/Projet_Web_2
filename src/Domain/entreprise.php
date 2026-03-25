<?php

namespace App\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'entreprises')]
class Entreprise
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', nullable: false)]
    private string $nom;

    #[Column(type: 'string', nullable: false)]
    private string $telephone;

    #[Column(name: 'date_creation', type: 'date_immutable', nullable: false)]
    private DateTimeImmutable $dateCreation;

    #[Column(type: 'string', nullable: false)]
    private string $type;

    #[Column(type: 'string', nullable: false)]
    private string $ville;

    #[Column(type: 'integer', nullable: false)]
    private int $salaries;

    #[Column(type: 'text', nullable: false)]
    private string $description;

    #[Column(type: 'text', nullable: false)]
    private string $missions;

    #[Column(name: 'domaines_expertise', type: 'text', nullable: false)]
    private string $domainesExpertise;

    #[Column(type: 'string', nullable: false)]
    private string $evaluation;

    #[Column(type: 'string', nullable: false)]
    private string $email;

    #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $nom,
        string $telephone,
        DateTimeImmutable $dateCreation,
        string $type,
        string $ville,
        int $salaries,
        string $description,
        string $missions,
        string $domainesExpertise,
        string $evaluation,
        string $email
    ) {
        $this->nom = $nom;
        $this->telephone = $telephone;
        $this->dateCreation = $dateCreation;
        $this->type = $type;
        $this->ville = $ville;
        $this->salaries = $salaries;
        $this->description = $description;
        $this->missions = $missions;
        $this->domainesExpertise = $domainesExpertise;
        $this->evaluation = $evaluation;
        $this->email = $email;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getDateCreation(): DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(DateTimeImmutable $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getVille(): string
    {
        return $this->ville;
    }

    public function setVille(string $ville): void
    {
        $this->ville = $ville;
    }

    public function getSalaries(): int
    {
        return $this->salaries;
    }

    public function setSalaries(int $salaries): void
    {
        $this->salaries = $salaries;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getMissions(): string
    {
        return $this->missions;
    }

    public function setMissions(string $missions): void
    {
        $this->missions = $missions;
    }

    public function getDomainesExpertise(): string
    {
        return $this->domainesExpertise;
    }

    public function setDomainesExpertise(string $domainesExpertise): void
    {
        $this->domainesExpertise = $domainesExpertise;
    }

    public function getEvaluation(): string
    {
        return $this->evaluation;
    }

    public function setEvaluation(string $evaluation): void
    {
        $this->evaluation = $evaluation;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}