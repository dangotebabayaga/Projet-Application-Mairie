<?php
namespace App\Entity;
use App\Repository\ReseauSocialeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReseauSocialeRepository::class)]
#[ORM\Table(name: 'reseau_sociale')]
class ReseauSociale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $villeId = null;

    #[ORM\Column(nullable: true)]
    private ?string $plateform = null;

    #[ORM\Column(nullable: true)]
    private ?string $lien = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleId(): ?int
    {
        return $this->villeId;
    }

    public function setVilleId(?int $villeId)
    {
        $this->villeId = $villeId;
    }

    public function getPlateform(): ?string
    {
        return $this->plateform;
    }

    public function setPlateform(?string $plateform)
    {
        $this->plateform = $plateform;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien)
    {
        $this->lien = $lien;
    }
}
