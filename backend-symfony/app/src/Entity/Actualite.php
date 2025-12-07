<?php

namespace App\Entity;

use App\Repository\ActualiteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActualiteRepository::class)]
#[ORM\Table(name: 'actualites')]
class Actualite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(name: 'type_source', length: 50)]
    private ?string $typeSource = null;

    #[ORM\Column(name: 'url_source', length: 500, nullable: true)]
    private ?string $urlSource = null;

    #[ORM\Column(name: 'date_publication', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'actualites')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'auteur_id', nullable: true)]
    private ?Utilisateur $auteur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getTypeSource(): string
    {
        return $this->typeSource;
    }

    public function setTypeSource(string $typeSource): static
    {
        $this->typeSource = $typeSource;
        return $this;
    }

    public function getUrlSource(): ?string
    {
        return $this->urlSource;
    }

    public function setUrlSource(?string $urlSource): static
    {
        $this->urlSource = $urlSource;
        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(?\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;
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

    public function getAuteur(): ?Utilisateur
    {
        return $this->auteur;
    }

    public function setAuteur(?Utilisateur $auteur): static
    {
        $this->auteur = $auteur;
        return $this;
    }

}