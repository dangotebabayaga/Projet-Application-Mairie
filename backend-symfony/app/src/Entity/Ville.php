<?php
namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[ORM\Table(name: 'ville')] // correction : 'Ville' → 'ville'
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slogan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(name: 'themeCouleur', length: 255, nullable: true)] // correction : nom de colonne conforme au SQL
    private ?string $themeCouleur = null;

    #[ORM\Column(type: 'datetime', name: 'date_creation', nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(?string $slogan): void
    {
        $this->slogan = $slogan;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): void
    {
        $this->logo = $logo;
    }

    public function getThemeCouleur(): ?string // correction : getThemecouleur → getThemeCouleur
    {
        return $this->themeCouleur;
    }

    public function setThemeCouleur(?string $themeCouleur): void // correction : setThemecouleur → setThemeCouleur
    {
        $this->themeCouleur = $themeCouleur;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }
}