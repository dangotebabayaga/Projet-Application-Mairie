<?php
namespace App\Entity;

use App\Repository\CategorieCitoyenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieCitoyenRepository::class)]
#[ORM\Table(name: 'categories_citoyens')]
class CategorieCitoyen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(name: 'ville_id', nullable: true)]
    private ?int $villeId = null;

    public function getId(): ?int { return $this->id; }
    public function getLibelle(): ?string { return $this->libelle; }
    public function setLibelle(?string $libelle): self { $this->libelle = $libelle; return $this; }
    public function getVilleId(): ?int { return $this->villeId; }
    public function setVilleId(?int $villeId): self { $this->villeId = $villeId; return $this; }
}
