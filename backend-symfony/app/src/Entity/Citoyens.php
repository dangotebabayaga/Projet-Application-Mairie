<?php
namespace App\Entity;
use App\Repository\CitoyensRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CitoyensRepository::class)]
#[ORM\Table(name: 'citoyens')]
class Citoyens
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
