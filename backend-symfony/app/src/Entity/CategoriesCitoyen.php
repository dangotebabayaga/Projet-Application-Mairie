<?php

namespace App\Entity;

use App\Repository\CategoriesCitoyenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesCitoyenRepository::class)]
#[ORM\Table(name: 'categories_citoyens')]
class CategoriesCitoyen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'categoriesCitoyens')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;
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