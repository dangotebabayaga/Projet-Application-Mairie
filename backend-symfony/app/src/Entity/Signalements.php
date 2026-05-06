<?php
namespace App\Entity;

use App\Repository\SignalementsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SignalementsRepository::class)]
#[ORM\Table(name: 'signalements')]
class Signalements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;
    
    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    // correction : id brut → relation ManyToOne vers TypesSignalement
    #[ORM\ManyToOne(targetEntity: TypesSignalement::class)]
    #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id', nullable: true)]
    private ?TypesSignalement $type = null;

    // correction : id brut → relation ManyToOne vers Utilisateur
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id', nullable: true)]
    private ?Utilisateur $utilisateur= null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateModification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): void
    {
        $this->titre = $titre;
    }

    public function getEtat(): EtatSignalement
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): void
    {
        $this->etat = $etat;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getType(): ?TypesSignalement
    {
        return $this->type;
    }

    public function setType(?TypesSignalement $type): void
    {
        $this->type = $type;
    }

    // correction : getCitoyenId/?int → getUtilisateur/?Utilisateur
    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): void
    {
        $this->Utilisateur= $utilisateur;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): void
    {
        $this->dateModification = $dateModification;
    }
}