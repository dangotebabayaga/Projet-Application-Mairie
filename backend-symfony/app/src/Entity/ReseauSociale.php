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

}
