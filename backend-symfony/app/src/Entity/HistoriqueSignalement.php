<?php

namespace App\Entity;

use App\Repository\HistoriqueSignalementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueSignalementRepository::class)]
#[ORM\Table(name: 'historique_signalements')]
class HistoriqueSignalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Signalement::class)]
    #[ORM\JoinColumn(name: 'signalement_id', nullable: false)]
    private ?Signalement $signalement = null;

    #[ORM\Column(name: 'ancien_etat', length: 50, nullable: true)]
    private ?string $ancienEtat = null;

    #[ORM\Column(name: 'nouvel_etat', length: 50)]
    private ?string $nouvelEtat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'modifie_par', nullable: true)]
    private ?Utilisateur $modifiePar = null;

    #[ORM\Column(name: 'date_modification', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAncienEtat(): ?string
    {
        return $this->ancienEtat;
    }

    public function setAncienEtat(?string $ancienEtat): static
    {
        $this->ancienEtat = $ancienEtat;
        return $this;
    }

    public function getNouvelEtat(): string
    {
        return $this->nouvelEtat;
    }

    public function setNouvelEtat(string $nouvelEtat): static
    {
        $this->nouvelEtat = $nouvelEtat;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
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

    public function getSignalement(): ?Signalement
    {
        return $this->signalement;
    }

    public function setSignalement(?Signalement $signalement): static
    {
        $this->signalement = $signalement;
        return $this;
    }

    public function getModifiePar(): ?Utilisateur
    {
        return $this->modifiePar;
    }

    public function setModifiePar(?Utilisateur $modifiePar): static
    {
        $this->modifiePar = $modifiePar;
        return $this;
    }
}