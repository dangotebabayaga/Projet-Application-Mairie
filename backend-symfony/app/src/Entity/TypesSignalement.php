<?php
namespace App\Entity;

use App\Repository\TypesSignalementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypesSignalementRepository::class)]
#[ORM\Table(name: 'types_signalement')]
class TypesSignalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)] // correction : length: 100 conforme au SQL
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