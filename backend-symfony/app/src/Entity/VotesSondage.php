<?php
namespace App\Entity;

use App\Repository\VotesSondageRepository;
use App\Entity\Utilisateur;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VotesSondageRepository::class)]
#[ORM\Table(name: 'votes_sondage')]
class VotesSondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // correction : Citoyens → Utilisateur+ referencedColumnName 'utilisateur_id' → 'id'
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id', nullable: false)]
    private ?Utilisateur $utilisateur= null;

    #[ORM\ManyToOne(targetEntity: Sondages::class)]
    #[ORM\JoinColumn(name: 'id_sondage', referencedColumnName: 'id', nullable: false)]
    private ?Sondages $sondage = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateVote = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    // correction : getCitoyen/setCitoyen → getUtilisateur/setUtilisateur
    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->Utilisateur= $utilisateur;
        return $this;
    }

    public function getSondage(): ?Sondages
    {
        return $this->sondage;
    }

    public function setSondage(?Sondages $sondage): self
    {
        $this->sondage = $sondage;
        return $this;
    }

    public function getDateVote(): ?\DateTimeInterface
    {
        return $this->dateVote;
    }

    public function setDateVote(?\DateTimeInterface $dateVote): self
    {
        $this->dateVote = $dateVote;
        return $this;
    }
}