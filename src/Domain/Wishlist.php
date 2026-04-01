<?php

namespace App\Domain;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[Entity]
#[Table(name: 'wishlists')]
#[UniqueConstraint(name: 'unique_user_offre', columns: ['user_id', 'offre_id'])]
class Wishlist
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ManyToOne(targetEntity: Offre::class)]
    #[JoinColumn(name: 'offre_id', referencedColumnName: 'id', nullable: false)]
    private Offre $offre;

    public function __construct(User $user, Offre $offre)
    {
        $this->user = $user;
        $this->offre = $offre;
    }

    public function getId(): int { return $this->id; }
    public function getUser(): User { return $this->user; }
    public function getOffre(): Offre { return $this->offre; }
}