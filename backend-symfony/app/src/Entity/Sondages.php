<?php
namespace App\Entity;

use App\Repository\SondagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Sondages::class)]
#[ORM\Table(name: 'sondages')]
class Sondages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'date_debut', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'date_fin', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    // correction : administrateur → administrateur
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'administrateur_id', referencedColumnName: 'id', nullable: true)]
    private ?Utilisateur $administrateur = null;

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): void { $this->titre = $titre; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(?\DateTimeInterface $dateDebut): void { $this->dateDebut = $dateDebut; }

    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }
    public function setDateFin(?\DateTimeInterface $dateFin): void { $this->dateFin = $dateFin; }

    // correction : getadministrateur → getAdministrateur
    public function getAdministrateur(): ?Utilisateur { return $this->administrateur; }
    public function setAdministrateur(?Utilisateur $administrateur): void { $this->administrateur = $administrateur; }
}