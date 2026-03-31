<?php

namespace App\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

#[Entity, Table(name: 'campus')]
class Campus 
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', nullable: false)]
    private string $ville;

    #[ManyToOne(targetEntity: Offre::class)]
    #[JoinColumn(name: 'idOffre', referencedColumnName: 'idOffre', nullable: false)]
    private Offre $offre;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: false)]
    private User $user;

    public function __construct(string $ville, User $user, Offre $offre) 
    {
        $this->ville = $ville;
        $this->user = $user;
        $this->offre = $offre;
    }


    public function getId(): int 
    { 
        return $this->id; 
    }

    public function getVille(): string 
    { 
        return $this->ville; 
    }

    public function getOffre(): Offre 
    { 
        return $this->offre; 
    }

    public function getUser(): User 
    { 
        return $this->user; 
    }

    public function setVille(string $ville): self 
    {
        $this->ville = $ville;
        return $this;
    }

    public function setOffre(Offre $offre): self 
    {
        $this->offre = $offre;
        return $this;
    }

    public function setUser(User $user): self 
    {
        $this->user = $user;
        return $this;
    }
} 
