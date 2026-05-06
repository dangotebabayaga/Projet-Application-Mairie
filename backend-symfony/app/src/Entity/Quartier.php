<?php
namespace App\Entity;

use App\Repository\QuartierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuartierRepository::class)]
#[ORM\Table(name: 'quartiers')]
class Quartier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(name: 'ville_id', nullable: true)]
    private ?int $villeId = null;

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): self { $this->nom = $nom; return $this; }
    public function getVilleId(): ?int { return $this->villeId; }
    public function setVilleId(?int $villeId): self { $this->villeId = $villeId; return $this; }
}
