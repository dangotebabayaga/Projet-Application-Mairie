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
    
    #[ORM\Column(type: "string", enumType: EtatSignalement::class)]
    private EtatSignalement $etat;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;
    
    #[ORM\Column(type: "decimal", precision: 10, scale: 7, nullable: true)]
    private ?float $latitude = null;
    
    #[ORM\Column(type: "decimal", precision: 10, scale: 7, nullable: true)]
    private ?float $longitude = null;
    
    #[ORM\Column(nullable: true)]
    private ?int $typeId = null;
    
    #[ORM\ManyToOne(targetEntity: Citoyens::class)]
    #[ORM\JoinColumn(name: "citoyen_id", referencedColumnName: "utilisateur_id")]
    private ?Citoyens $citoyen = null; 
    // **Ajoute explicitement le type datetime**
    #[ORM\Column(type: "datetime", nullable: true)]
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

    public function getEtat(): EtatSignalement
    {
        return $this->etat;
    }
    
    public function setEtat(EtatSignalement $etat): self
    {
        $this->etat = $etat;
        return $this;
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

    // getter et setter
    public function getCitoyen(): ?Citoyens
    {
        return $this->citoyen;
    }
    
    public function setCitoyen(?Citoyens $citoyen): self
    {
        $this->citoyen = $citoyen;
        return $this;
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


}
