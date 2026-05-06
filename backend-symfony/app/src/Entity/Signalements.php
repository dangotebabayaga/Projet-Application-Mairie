<?php
namespace App\Entity;

use App\Enum\EtatSignalement;
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

    // correction : string → enum EtatSignalement
    #[ORM\Column(type: 'string', enumType: EtatSignalement::class, nullable: true)]
    private ?EtatSignalement $etat = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\ManyToOne(targetEntity: TypesSignalement::class)]
    #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id', nullable: true)]
    private ?TypesSignalement $type = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id', nullable: true)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;
    
    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dateModification = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre)
    {
        $this->titre = $titre;
    }

    // correction : type de retour ?EtatSignalement
    public function getEtat(): ?EtatSignalement
    {
        return $this->etat;
    }

    public function setEtat(?EtatSignalement $etat): void
    {
        $this->etat = $etat;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude)
    {
        $this->longitude = $longitude;
    }

    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    public function setTypeId(?int $typeId)
    {
        $this->typeId = $typeId;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->citoyen;
    }
    
    public function setCitoyen(?Citoyens $citoyen): self
    {
        $this->utilisateur = $utilisateur; // correction : $this->Utilisateur → $this->utilisateur
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeInterface $dateModification)
    {
        $this->dateModification = $dateModification;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }
}
