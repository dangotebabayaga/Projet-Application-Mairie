<?php

namespace App\Entity;

use App\Repository\QuestionsSondageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionsSondageRepository::class)]
#[ORM\Table(name: 'questions_sondage')]
class QuestionsSondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $question = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $ordre = null;

    #[ORM\ManyToOne(targetEntity: Sondage::class)]
    #[ORM\JoinColumn(name: 'sondage_id', nullable: false)]
    private ?Sondage $sondage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;
        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): static
    {
        $this->ordre = $ordre;
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

}