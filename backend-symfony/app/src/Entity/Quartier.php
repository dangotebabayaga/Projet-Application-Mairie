<?php

namespace App\Entity;

use App\Repository\QuartierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuartierRepository::class)]
#[ORM\Table(name: 'quartiers')]
class Quartier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'quartiers')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

}