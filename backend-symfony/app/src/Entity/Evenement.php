<?php
namespace App\Entity;
use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[ORM\Table(name: 'Evenement')]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $dateev = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $heuredeb = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $heurefin = null;

    #[ORM\Column(nullable: true)]
    private ?int $administrateurId = null;

    #[ORM\Column(nullable: true)]
    private ?int $villeId = null;

    #[ORM\Column(nullable: true)]
    private ?int $type = null;

}
