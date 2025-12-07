<?php

namespace App\Entity;

use App\Repository\ReseauSocialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReseauSocialRepository::class)]
#[ORM\Table(name: 'reseaux_sociaux')]
class ReseauSocial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $plateforme = null;

    #[ORM\Column(length: 500)]
    private ?string $url = null;

    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'reseauxSociaux')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlateforme(): string
    {
        return $this->plateforme;
    }

    public function setPlateforme(string $plateforme): static
    {
        $this->plateforme = $plateforme;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

}