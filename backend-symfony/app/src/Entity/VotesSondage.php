<?php

namespace App\Entity;

use App\Repository\VotesSondageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VotesSondageRepository::class)]
#[ORM\Table(name: 'votes_sondage')]
class VotesSondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'date_vote', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateVote = null;

    #[ORM\ManyToOne(targetEntity: Sondage::class)]
    #[ORM\JoinColumn(name: 'sondage_id', nullable: false)]
    private ?Sondage $sondage = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'citoyen_id', nullable: false)]
    private ?Utilisateur $citoyen = null;

    #[ORM\ManyToOne(targetEntity: QuestionsSondage::class)]
    #[ORM\JoinColumn(name: 'question_id', nullable: false)]
    private ?QuestionsSondage $question = null;

    #[ORM\ManyToOne(targetEntity: ReponsesSondage::class)]
    #[ORM\JoinColumn(name: 'reponse_id', nullable: false)]
    private ?ReponsesSondage $reponse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateVote(): ?\DateTimeInterface
    {
        return $this->dateVote;
    }

    public function setDateVote(?\DateTimeInterface $dateVote): static
    {
        $this->dateVote = $dateVote;
        return $this;
    }

    public function getSondage(): ?Sondage
    {
        return $this->sondage;
    }

    public function setSondage(?Sondage $sondage): static
    {
        $this->sondage = $sondage;
        return $this;
    }

    public function getCitoyen(): ?Utilisateur
    {
        return $this->citoyen;
    }

    public function setCitoyen(?Utilisateur $citoyen): static
    {
        $this->citoyen = $citoyen;
        return $this;
    }

    public function getQuestion(): ?QuestionsSondage
    {
        return $this->question;
    }

    public function setQuestion(?QuestionsSondage $question): static
    {
        $this->question = $question;
        return $this;
    }

    public function getReponse(): ?ReponsesSondage
    {
        return $this->reponse;
    }

    public function setReponse(?ReponsesSondage $reponse): static
    {
        $this->reponse = $reponse;
        return $this;
    }

}