<?php
namespace App\Entity;
use App\Repository\ReponsesSondageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponsesSondageRepository::class)]
#[ORM\Table(name: 'reponses_sondage')]
class ReponsesSondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $questionId = null;

    #[ORM\Column(nullable: true)]
    private ?string $libelle = null;

}
