<?php
namespace App\Entity;
use App\Repository\CitoyensRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CitoyensRepository::class)]
#[ORM\Table(name: 'citoyens')]
class Citoyens
{
    #[ORM\Id]
    #[ORM\Column(type: "integer", name: "utilisateur_id")]
    private ?int $utilisateurId = null;

    public function getUtilisateurId(): ?int
    {
        return $this->utilisateurId;
    }

    public function setUtilisateurId(?int $utilisateurId)
    {
        $this->utilisateurId = $utilisateurId;
    }

}
