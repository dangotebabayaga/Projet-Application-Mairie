<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[ORM\Table(name: 'villes')]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'nom_ville', length: 255)]
    private ?string $nomVille = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $slogan = null;

    #[ORM\Column(name: 'logo_url', length: 500, nullable: true)]
    private ?string $logoUrl = null;

    #[ORM\Column(name: 'couleurs_theme', length: 100, nullable: true)]
    private ?string $couleursTheme = null;

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomVille(): string
    {
        return $this->nomVille;
    }

    public function setNomVille(string $nomVille): static
    {
        $this->nomVille = $nomVille;
        return $this;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(?string $slogan): static
    {
        $this->slogan = $slogan;
        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): static
    {
        $this->logoUrl = $logoUrl;
        return $this;
    }

    public function getCouleursTheme(): ?string
    {
        return $this->couleursTheme;
    }

    public function setCouleursTheme(?string $couleursTheme): static
    {
        $this->couleursTheme = $couleursTheme;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }



    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Quartier::class)]
    private Collection $quartiers;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: CategoriesCitoyen::class)]
    private Collection $categoriesCitoyens;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: ThematiquesEvenement::class)]
    private Collection $thematiquesEvenements;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Utilisateur::class)]
    private Collection $utilisateurs;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Sondage::class)]
    private Collection $sondages;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Evenement::class)]
    private Collection $evenements;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Actualite::class)]
    private Collection $actualites;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: ReseauSocial::class)]
    private Collection $reseauxSociaux;

    public function __construct()
    {
        $this->quartiers = new ArrayCollection();
        $this->categoriesCitoyens = new ArrayCollection();
        $this->thematiquesEvenements = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->sondages = new ArrayCollection();
        $this->evenements = new ArrayCollection();
        $this->actualites = new ArrayCollection();
        $this->reseauxSociaux = new ArrayCollection();
    }

    public function getQuartiers(): Collection { return $this->quartiers; }
    public function getCategoriesCitoyens(): Collection { return $this->categoriesCitoyens; }
    public function getThematiquesEvenements(): Collection { return $this->thematiquesEvenements; }
    public function getUtilisateurs(): Collection { return $this->utilisateurs; }
    public function getSondages(): Collection { return $this->sondages; }
    public function getEvenements(): Collection { return $this->evenements; }
    public function getActualites(): Collection { return $this->actualites; }
    public function getReseauxSociaux(): Collection { return $this->reseauxSociaux; }

}