<?php
namespace App\Entity;

use App\Repository\SuperAdministrateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuperAdministrateurRepository::class)]
#[ORM\Table(name: 'super_administrateurs')]
class SuperAdministrateur
{
    #[ORM\Id]
    #[ORM\Column(name: 'utilisateur_id', type: 'integer')]
    private ?int $utilisateurId = null;

    public function getUtilisateurId(): ?int
    {
        return $this->utilisateurId;
    }

    public function setUtilisateurId(?int $utilisateurId): self
    {
        $this->utilisateurId = $utilisateurId;
        return $this;
    }
}
