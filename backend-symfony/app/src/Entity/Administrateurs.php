<?php
namespace App\Entity;
use App\Repository\AdministrateursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdministrateursRepository::class)]
#[ORM\Table(name: 'administrateurs')]
class Administrateurs
{
    #[ORM\Column(nullable: true)]
    private ?int $utilisateurId = null;

    #[ORM\Column(nullable: true)]
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
