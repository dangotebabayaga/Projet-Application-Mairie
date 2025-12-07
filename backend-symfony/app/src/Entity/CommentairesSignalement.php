<?php

namespace App\Entity;

use App\Repository\CommentairesSignalementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentairesSignalementRepository::class)]
#[ORM\Table(name: 'commentaires_signalement')]
class CommentairesSignalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $modifie = null;

    #[ORM\Column(name: 'date_modification', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
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

    public function isModifie(): ?bool
    {
        return $this->modifie;
    }

    public function setModifie(?bool $modifie): static
    {
        $this->modifie = $modifie;
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

    #[ORM\ManyToOne(targetEntity: Signalement::class)]
    #[ORM\JoinColumn(name: 'signalement_id', nullable: false)]
    private ?Signalement $signalement = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'auteur_id', nullable: false)]
    private ?Utilisateur $auteur = null;

    public function getSignalement(): ?Signalement
    {
        return $this->signalement;
    }

    public function setSignalement(?Signalement $signalement): static
    {
        $this->signalement = $signalement;
        return $this;
    }

    public function getAuteur(): ?Utilisateur
    {
        return $this->auteur;
    }

    public function setAuteur(?Utilisateur $auteur): static
    {
        $this->auteur = $auteur;
        return $this;
    }

}