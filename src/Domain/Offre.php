<?php

namespace App\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'offres')]
class Offre
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ManyToOne(targetEntity: Entreprise::class, inversedBy: 'offres')]
    
    #[JoinColumn(name: 'entreprise_id', referencedColumnName: 'id', nullable: false)]
    private Entreprise $entreprise;

    #[Column(type: 'string', nullable: false)]
    private string $titre;

    #[Column(type: 'string', nullable: false)]
    private string $telephone;

    #[Column(type: 'string', nullable: false)]
    private string $dateDebut;

    #[Column(type: 'string', nullable: false)]
    private string $duree;

    #[Column(type: 'string', nullable: false)]
    private string $ville;

    #[Column(type: 'string', nullable: false)]
    private string $remuneration;

    #[Column(type: 'text', nullable: false)]
    private string $description;

    #[Column(type: 'string', nullable: false)]
    private string $missions;

    #[Column(type: 'string', nullable: false)]
    private string $niveau;

    #[Column(type: 'string', nullable: false)]
    private string $competences;

    #[Column(type: 'string', nullable: false)]
    private string $email;

    public function __construct(
        Entreprise $entreprise,
        string $titre,
        string $telephone,
        string $dateDebut,
        string $duree,
        string $ville,
        string $remuneration,
        string $description,
        string $missions,
        string $niveau,
        string $competences,
        string $email,
    ) 
    {
        $this->entreprise = $entreprise;
        $this->titre = $titre;
        $this->telephone = $telephone;
        $this->dateDebut = $dateDebut;
        $this->duree = $duree;
        $this->ville = $ville;
        $this->remuneration = $remuneration;
        $this->description = $description;
        $this->missions = $missions;
        $this->niveau = $niveau;
        $this->competences = $competences;
        $this->email = $email;
    }

    public function getId(): int 
    { 
        return $this->id; 
    }

    public function getEntreprise(): Entreprise 
    { 
        return $this->entreprise; 
    }
    public function setEntreprise(Entreprise $entreprise): void 
    { 
        $this->entreprise = $entreprise; 
    }

    public function getTitre(): string 
    { 
        return $this->titre; 
    }
    public function setTitre(string $titre): void 
    { 
        $this->titre = $titre; 
    }

    public function getTelephone(): string 
    { 
        return $this->telephone; 
    }
    public function setTelephone(string $telephone): void 
    { 
        $this->telephone = $telephone; 
    }

    public function getDateDebut(): string
    { 
        return $this->dateDebut; 
    }
    public function setDateDebut(string $dateDebut): void
    { 
        $this->dateDebut = $dateDebut; 
    }

    public function getDuree(): string
    { 
        return $this->duree; 
    }
    public function setDuree(string $duree): void
    { 
        $this->duree = $duree; 
    }

    public function getVille(): string 
    { 
        return $this->ville; 
    }
    public function setVille(string $ville): void 
    { 
        $this->ville = $ville; 
    }

    public function getRemuneration(): string 
    { 
        return $this->remuneration; 
    }
    public function setRemuneration(string $remuneration): void
    { 
        $this->remuneration = $remuneration; 
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

    public function getNiveau(): string 
    { 
        return $this->niveau; 
    }
    public function setNiveau(string $niveau): void 
    { 
        $this->niveau = $niveau; 
    }

    public function getCompetences(): string 
    { 
        return $this->competences; 
    }
    public function setCompetences(string $competences): void 
    { 
        $this->competences = $competences; 
    }

    public function getEmail(): string 
    { 
        return $this->email; 
    }
    public function setEmail(string $email): void 
    { 
        $this->email = $email; 
    }
}