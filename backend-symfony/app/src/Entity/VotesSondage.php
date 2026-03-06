<?php
namespace App\Entity;

use App\Repository\VotesSondageRepository;
use App\Entity\Citoyens;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VotesSondageRepository::class)]
#[ORM\Table(name: 'votes_sondage')]
class VotesSondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Citoyens::class)]
    #[ORM\JoinColumn(name: 'citoyen_id', referencedColumnName: 'utilisateur_id', nullable: false)]
    private Citoyens $citoyen;

    #[ORM\ManyToOne(targetEntity: Sondages::class)]
    #[ORM\JoinColumn(name: 'id_sondage', referencedColumnName: 'id', nullable: false)]
    private Sondages $sondage;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateVote;
// getters et setters
    public function getCitoyen(): Citoyens { return $this->citoyen; }
    public function setCitoyen(Citoyens $citoyen): self { $this->citoyen = $citoyen; return $this; }

    public function getSondage(): Sondages { return $this->sondage; }
    public function setSondage(Sondages $sondage): self { $this->sondage = $sondage; return $this; }

    public function getDateVote(): \DateTimeInterface { return $this->dateVote; }
    public function setDateVote(\DateTimeInterface $dateVote): self { $this->dateVote = $dateVote; return $this; }
}

