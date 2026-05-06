<?php
namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[ORM\Table(name: 'evenement')]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaire = null; // <-- texte long

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieux = null;

    #[ORM\Column(type: 'date',name:"dateev", nullable: true)]
    private ?\DateTimeInterface $dateEv = null;

    #[ORM\Column(type: 'time',name:"heuredeb", nullable: true)]
    private ?\DateTimeInterface $heureDeb = null;

    #[ORM\Column(type: 'time',name:"heurefin", nullable: true)]
    private ?\DateTimeInterface $heureFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

    // correction : id brut → relation ManyToOne vers Utilisateur
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'administrateur_id', referencedColumnName: 'id', nullable: true)]
    private ?Utilisateur $administrateur = null;
    // correction : id brut → relation ManyToOne vers Utilisateur
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'administrateur_id', referencedColumnName: 'id', nullable: true)]
    private ?Utilisateur $administrateur = null;

    // correction : id brut → relation ManyToOne vers TypeEv
    #[ORM\ManyToOne(targetEntity: TypeEv::class)]
    #[ORM\JoinColumn(name: 'type', referencedColumnName: 'id', nullable: true)]
    private ?TypeEv $type = null;
    // correction : id brut → relation ManyToOne vers TypeEv
    #[ORM\ManyToOne(targetEntity: TypeEv::class)]
    #[ORM\JoinColumn(name: 'type', referencedColumnName: 'id', nullable: true)]
    private ?TypeEv $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getLieux(): ?string
    {
        return $this->lieux;
    }

    public function setLieux(?string $lieux): self
    {
        $this->lieux = $lieux;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->img;
    }

    public function setImage(?string $img): self
    {
        $this->img = $img;
        return $this;
    }

    public function getDateEv(): ?\DateTimeInterface
    {
        return $this->dateEv;
    }

    public function setDateEv(?\DateTimeInterface $dateEv): self
    {
        $this->dateEv = $dateEv;
        return $this;
    }

    public function getHeureDeb(): ?\DateTimeInterface
    {
        return $this->heureDeb;
    }

    public function setHeureDeb(?\DateTimeInterface $heureDeb): self
    {
        $this->heureDeb = $heureDeb;
        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(?\DateTimeInterface $heureFin): self
    {
        $this->heureFin = $heureFin;
        return $this;
    }

    // correction : ?int → ?Utilisateur
    public function getadministrateur(): ?Utilisateur
    // correction : ?int → ?Utilisateur
    public function getadministrateur(): ?Utilisateur
    {
        return $this->administrateur;
        return $this->administrateur;
    }

    public function setadministrateur(?Utilisateur $administrateur): self
    public function setadministrateur(?Utilisateur $administrateur): self
    {
        $this->administrateur = $administrateur;
        $this->administrateur = $administrateur;
        return $this;
    }

    // correction : ?int → ?TypeEv
    public function getType(): ?TypeEv
    // correction : ?int → ?TypeEv
    public function getType(): ?TypeEv
    {
        return $this->type;
    }

    public function setType(?TypeEv $type): self
    public function setType(?TypeEv $type): self
    {
        $this->type = $type;
        return $this;
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
}