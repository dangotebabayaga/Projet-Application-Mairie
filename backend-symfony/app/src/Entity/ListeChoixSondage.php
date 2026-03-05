<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'liste_choix_sondage')]
class ListeChoixSondage
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Sondages::class, inversedBy: 'listeChoix')]
    #[ORM\JoinColumn(name: 'id_sondage', referencedColumnName: 'id', nullable: false)]
    private Sondages $sondage;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Choix::class)]
    #[ORM\JoinColumn(name: 'id_choix', referencedColumnName: 'id', nullable: false)]
    private Choix $choix;

    public function getSondage(): Sondages
    {
        return $this->sondage;
    }

    public function setSondage(Sondages $sondage): self
    {
        $this->sondage = $sondage;
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