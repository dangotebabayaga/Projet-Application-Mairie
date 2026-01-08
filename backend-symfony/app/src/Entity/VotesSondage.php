<?php
namespace App\Entity;

use App\Repository\VotesSondageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VotesSondageRepository::class)]
#[ORM\Table(name: 'votes_sondage')]
class VotesSondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $citoyenId = null;

    #[ORM\Column(nullable: true)]
    private ?int $questionId = null;

    #[ORM\Column(nullable: true)]
    private ?int $reponseId = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $dateVote = null;

}
