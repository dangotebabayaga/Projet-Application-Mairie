<?php

namespace App\Entity;

use App\Repository\SondageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SondageRepository::class)]
#[ORM\Table(name: 'sondages')]
class Sondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'date_debut', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'date_fin', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'sondages')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(targetEntity: Quartier::class)]
    #[ORM\JoinColumn(name: 'quartier_id', nullable: true)]
    private ?Quartier $quartier = null;

    #[ORM\ManyToOne(targetEntity: CategoriesCitoyen::class)]
    #[ORM\JoinColumn(name: 'categorie_id', nullable: true)]
    private ?CategoriesCitoyen $categorie = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'cree_par', nullable: false)]
    private ?Utilisateur $creePar = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
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

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): static
    {
        $this->quartier = $quartier;
        return $this;
    }

    public function getCategorie(): ?CategoriesCitoyen
    {
        return $this->categorie;
    }

    public function setCategorie(?CategoriesCitoyen $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getCreePar(): ?Utilisateur
    {
        return $this->creePar;
    }

    public function setCreePar(?Utilisateur $creePar): static
    {
        $this->creePar = $creePar;
        return $this;
    }

}