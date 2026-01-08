<?php
namespace App\Entity;

use App\Repository\TypeEvRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeEvRepository::class)]
#[ORM\Table(name: 'Type_ev')]
class TypeEv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $nom = null;

}
