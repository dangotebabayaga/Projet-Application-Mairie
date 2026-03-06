<?php
namespace App\Entity;
use App\Repository\AdministrateursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdministrateursRepository::class)]
#[ORM\Table(name: 'administrateurs')]
class Admin
{
    #[ORM\Id]
    #[ORM\Column(name: "utilisateur_id", type: "integer")]
    private ?int $utilisateurId = null;

    #[ORM\Column(name: "ville_id", type: "integer")]
    private ?int $villeId = null;

    public function getUtilisateurId(): ?int
    {
        return $this->utilisateurId;
    }

    public function setUtilisateurId(?int $utilisateurId)
    {
        $this->utilisateurId = $utilisateurId;
    }

    public function getVilleId(): ?int
    {
        return $this->villeId;
    }

    public function setVilleId(?int $villeId)
    {
        $this->villeId = $villeId;
    }


}
