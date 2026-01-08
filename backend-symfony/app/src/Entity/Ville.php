<?php
namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[ORM\Table(name: 'Ville')]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?string $slogan = null;

    #[ORM\Column(nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(nullable: true)]
    private ?string $themecouleur = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

}
