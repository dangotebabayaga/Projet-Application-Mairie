<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(name: 'mot_de_passe_hash', length: 255)]
    private ?string $motDePasseHash = null;

    #[ORM\Column(length: 50)]
    private ?string $role = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: 'date_naissance', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(name: 'consentement_rgpd', type: 'boolean')]
    private ?bool $consentementRgpd = null;

    #[ORM\Column(name: 'date_consentement_rgpd', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateConsentementRgpd = null;

    #[ORM\Column(name: 'derniere_connexion', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $derniereConnexion = null;

    #[ORM\Column(name: 'compte_actif', type: 'boolean', nullable: true)]
    private ?bool $compteActif = null;

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

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

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasseHash(): string
    {
        return $this->motDePasseHash;
    }

    public function setMotDePasseHash(string $motDePasseHash): static
    {
        $this->motDePasseHash = $motDePasseHash;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function isConsentementRgpd(): bool
    {
        return $this->consentementRgpd;
    }

    public function setConsentementRgpd(bool $consentementRgpd): static
    {
        $this->consentementRgpd = $consentementRgpd;
        return $this;
    }

    public function getDateConsentementRgpd(): ?\DateTimeInterface
    {
        return $this->dateConsentementRgpd;
    }

    public function setDateConsentementRgpd(?\DateTimeInterface $dateConsentementRgpd): static
    {
        $this->dateConsentementRgpd = $dateConsentementRgpd;
        return $this;
    }

    public function getDerniereConnexion(): ?\DateTimeInterface
    {
        return $this->derniereConnexion;
    }

    public function setDerniereConnexion(?\DateTimeInterface $derniereConnexion): static
    {
        $this->derniereConnexion = $derniereConnexion;
        return $this;
    }

    public function isCompteActif(): ?bool
    {
        return $this->compteActif;
    }

    public function setCompteActif(?bool $compteActif): static
    {
        $this->compteActif = $compteActif;
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

    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(targetEntity: Quartier::class)]
    #[ORM\JoinColumn(name: 'quartier_id', nullable: true)]
    private ?Quartier $quartier = null;

    #[ORM\ManyToOne(targetEntity: CategoriesCitoyen::class)]
    #[ORM\JoinColumn(name: 'categorie_id', nullable: true)]
    private ?CategoriesCitoyen $categorie = null;

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

}