<?php

namespace App\Entity;

use App\Repository\SignalementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SignalementRepository::class)]
#[ORM\Table(name: 'signalements')]
class Signalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: 'type_signalement', length: 50)]
    private ?string $typeSignalement = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = null;

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: 'date_modification', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModification = null;

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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;
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

    public function getTypeSignalement(): string
    {
        return $this->typeSignalement;
    }

    public function setTypeSignalement(string $typeSignalement): static
    {
        $this->typeSignalement = $typeSignalement;
        return $this;
    }

    public function getEtat(): string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
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

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): static
    {
        $this->dateModification = $dateModification;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Fichier::class)]
    #[ORM\JoinColumn(name: 'fichier_id', nullable: true)]
    private ?Fichier $fichier = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'citoyen_id', nullable: false)]
    private ?Utilisateur $citoyen = null;

    #[ORM\ManyToOne(targetEntity: Quartier::class)]
    #[ORM\JoinColumn(name: 'quartier_id', nullable: true)]
    private ?Quartier $quartier = null;

    public function getFichier(): ?Fichier
    {
        return $this->fichier;
    }

    public function setFichier(?Fichier $fichier): static
    {
        $this->fichier = $fichier;
        return $this;
    }

    public function getCitoyen(): ?Utilisateur
    {
        return $this->citoyen;
    }

    public function setCitoyen(?Utilisateur $citoyen): static
    {
        $this->citoyen = $citoyen;
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

}