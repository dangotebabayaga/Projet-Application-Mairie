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


    public function get_citoyenId(): ?int
    {
        return $this->citoyenId;
    }

    public function set_citoyenId(int $new_id)
    {
        $this->citoyenId = $new_id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCitoyenId(?int $citoyenId)
    {
        $this->citoyenId = $citoyenId;
    }

    public function getQuestionId(): ?int
    {
        return $this->questionId;
    }

    public function setQuestionId(?int $questionId)
    {
        $this->questionId = $questionId;
    }

    public function getReponseId(): ?int
    {
        return $this->reponseId;
    }

    public function setReponseId(?int $reponseId)
    {
        $this->reponseId = $reponseId;
    }

    public function getDateVote(): ?\DateTimeInterface
    {
        return $this->dateVote;
    }

    public function setDateVote(?\DateTimeInterface $dateVote)
    {
        $this->dateVote = $dateVote;
    }


}
