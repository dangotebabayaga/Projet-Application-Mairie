<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FichierRepository::class)]
#[ORM\Table(name: 'fichiers')]
class Fichier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'nom_original', length: 255)]
    private ?string $nomOriginal = null;

    #[ORM\Column(name: 'nom_stockage', length: 255)]
    private ?string $nomStockage = null;

    #[ORM\Column(length: 500)]
    private ?string $chemin = null;

    #[ORM\Column(name: 'type_mime', length: 100)]
    private ?string $typeMime = null;

    #[ORM\Column(name: 'taille_octets', type: 'bigint')]
    private ?int $tailleOctets = null;

    #[ORM\Column(name: 'hash_sha256', length: 64, nullable: true)]
    private ?string $hashSha256 = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'uploade_par', nullable: false)]
    private ?Utilisateur $uploadePar = null;

    #[ORM\Column(name: 'date_upload', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateUpload = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomOriginal(): string
    {
        return $this->nomOriginal;
    }

    public function setNomOriginal(string $nomOriginal): static
    {
        $this->nomOriginal = $nomOriginal;
        return $this;
    }

    public function getNomStockage(): string
    {
        return $this->nomStockage;
    }

    public function setNomStockage(string $nomStockage): static
    {
        $this->nomStockage = $nomStockage;
        return $this;
    }

    public function getChemin(): string
    {
        return $this->chemin;
    }

    public function setChemin(string $chemin): static
    {
        $this->chemin = $chemin;
        return $this;
    }

    public function getTypeMime(): string
    {
        return $this->typeMime;
    }

    public function setTypeMime(string $typeMime): static
    {
        $this->typeMime = $typeMime;
        return $this;
    }

    public function getTailleOctets(): int
    {
        return $this->tailleOctets;
    }

    public function setTailleOctets(int $tailleOctets): static
    {
        $this->tailleOctets = $tailleOctets;
        return $this;
    }

    public function getHashSha256(): ?string
    {
        return $this->hashSha256;
    }

    public function setHashSha256(?string $hashSha256): static
    {
        $this->hashSha256 = $hashSha256;
        return $this;
    }

    public function getDateUpload(): ?\DateTimeInterface
    {
        return $this->dateUpload;
    }

    public function setDateUpload(?\DateTimeInterface $dateUpload): static
    {
        $this->dateUpload = $dateUpload;
        return $this;
    }

    public function getUploadePar(): ?Utilisateur
    {
        return $this->uploadePar;
    }

    public function setUploadePar(?Utilisateur $uploadePar): static
    {
        $this->uploadePar = $uploadePar;
        return $this;
    }
}