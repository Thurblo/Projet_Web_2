<?php

namespace App\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'candidatures')]
class Candidature
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $etudiant;

    #[ManyToOne(targetEntity: Offre::class)]
    #[JoinColumn(name: 'offre_id', referencedColumnName: 'id', nullable: false)]
    private Offre $offre;

    #[Column(type: Types::STRING, length: 50)]
    private string $statut = 'en_attente';

    #[Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $datePostulation;

    public function __construct(User $etudiant, Offre $offre)
    {
        $this->etudiant = $etudiant;
        $this->offre = $offre;
        $this->datePostulation = new \DateTime();
    }

    public function getId(): int { return $this->id; }
    public function getEtudiant(): User { return $this->etudiant; }
    public function getOffre(): Offre { return $this->offre; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): void { $this->statut = $statut; }
    public function getDatePostulation(): \DateTime { return $this->datePostulation; }
}