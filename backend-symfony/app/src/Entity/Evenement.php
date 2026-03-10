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

    #[ORM\Column(name:"administrateur_id",nullable: true)]
    private ?int $administrateurId = null;

    #[ORM\Column(nullable: true)]
    private ?int $type = null;

    // ===== Getters & Setters =====

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

    public function getAdministrateurId(): ?int
    {
        return $this->administrateurId;
    }

    public function setAdministrateurId(?int $administrateurId): self
    {
        $this->administrateurId = $administrateurId;
        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;
        return $this;
    }
}