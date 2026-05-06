<?php
namespace App\Entity;

use App\Repository\TypeEvRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeEvRepository::class)]
#[ORM\Table(name: 'type_ev')] // correction : 'Type_ev' → 'type_ev' (cohérence avec le SQL)
class TypeEv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void // correction : ': void' manquant
    {
        $this->nom = $nom;
    }
}