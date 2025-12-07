<?php

namespace App\Entity;

use App\Repository\ReponsesSondageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponsesSondageRepository::class)]
#[ORM\Table(name: 'reponses_sondage')]
class ReponsesSondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'choix_reponse', type: Types::TEXT)]
    private ?string $choixReponse = null;

    #[ORM\ManyToOne(targetEntity: QuestionsSondage::class)]
    #[ORM\JoinColumn(name: 'question_id', nullable: false)]
    private ?QuestionsSondage $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChoixReponse(): string
    {
        return $this->choixReponse;
    }

    public function setChoixReponse(string $choixReponse): static
    {
        $this->choixReponse = $choixReponse;
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

}