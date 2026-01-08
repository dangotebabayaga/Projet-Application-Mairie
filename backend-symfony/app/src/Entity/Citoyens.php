<?php
namespace App\Entity;
use App\Repository\CitoyensRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CitoyensRepository::class)]
#[ORM\Table(name: 'citoyens')]
class Citoyens
{
    #[ORM\Column(nullable: true)]
    private ?int $utilisateurId = null;

    #[ORM\Column(nullable: true)]
    private ?int $villeId = null;

}
