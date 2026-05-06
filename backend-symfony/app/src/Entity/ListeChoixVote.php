<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'liste_choix_vote')]
class ListeChoixVote
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: VotesSondage::class)]
    #[ORM\JoinColumn(name: 'id_vote', referencedColumnName: 'id', nullable: false)]
    private VotesSondage $vote;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Choix::class)]
    #[ORM\JoinColumn(name: 'id_choix', referencedColumnName: 'id', nullable: false)]
    private Choix $choix;

    public function getVote(): VotesSondage
    {
        return $this->vote;
    }

    public function setVote(VotesSondage $vote): self
    {
        $this->vote = $vote;
        return $this;
    }

    public function getChoix(): Choix
    {
        return $this->choix;
    }

    public function setChoix(Choix $choix): self
    {
        $this->choix = $choix;
        return $this;
    }
}