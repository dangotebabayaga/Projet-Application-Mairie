<?php
namespace App\Entity;

use App\Repository\ReseauSocialeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReseauSocialeRepository::class)]
#[ORM\Table(name: 'reseau_sociale')]
class ReseauSociale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // correction : id brut → relation ManyToOne vers Ville
    #[ORM\ManyToOne(targetEntity: Ville::class)]
    #[ORM\JoinColumn(name: 'ville_id', referencedColumnName: 'id', nullable: true)]
    private ?Ville $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $plateforme = null; // correction : typo "plateform" → "plateforme"

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    // correction : ?int → ?Ville
    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): void
    {
        $this->ville = $ville;
    }

    public function getPlateforme(): ?string
    {
        return $this->plateforme;
    }

    public function setPlateforme(?string $plateforme): void
    {
        $this->plateforme = $plateforme;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): void
    {
        $this->lien = $lien;
    }
}