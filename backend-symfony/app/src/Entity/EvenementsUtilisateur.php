<?php

namespace App\Entity;

use App\Repository\EvenementsUtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementsUtilisateurRepository::class)]
#[ORM\Table(name: 'evenements_utilisateur')]
class EvenementsUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'date_ajout', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAjout = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'utilisateur_id', nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Evenement::class)]
    #[ORM\JoinColumn(name: 'evenement_id', nullable: false)]
    private ?Evenement $evenement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(?\DateTimeInterface $dateAjout): static
    {
        $this->dateAjout = $dateAjout;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;
        return $this;
    }

}