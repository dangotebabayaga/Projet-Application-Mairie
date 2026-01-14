<?php
namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[ORM\Table(name: 'Ville')]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?string $slogan = null;

    #[ORM\Column(nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(nullable: true)]
    private ?string $themecouleur = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom)
    {
        $this->nom = $nom;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(?string $slogan)
    {
        $this->slogan = $slogan;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo)
    {
        $this->logo = $logo;
    }

    public function getThemecouleur(): ?string
    {
        return $this->themecouleur;
    }

    public function setThemecouleur(?string $themecouleur)
    {
        $this->themecouleur = $themecouleur;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }


}
