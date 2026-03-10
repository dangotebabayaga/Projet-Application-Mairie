<?php
namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
class Utilisateurs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name:"mot_de_passe_hash",nullable: true)]
    private ?string $motDePasseHash = null;

    #[ORM\Column(type: "datetime", name:"date_creation",nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: "date", name:"date_naissance", nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: 'ville_id', nullable: true)]
    private ?int $villeId = null;

    #[ORM\Column(name:"compte_actif",nullable: true)]
    private ?bool $compteActif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom)
    {
        $this->nom = $nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom)
    {
        $this->prenom = $prenom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email)
    {
        $this->email = $email;
    }

    public function getMotDePasseHash(): ?string
    {
        return $this->motDePasseHash;
    }

    public function setMotDePasseHash(?string $motDePasseHash)
    {
        $this->motDePasseHash = $motDePasseHash;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone)
    {
        $this->telephone = $telephone;
    }

    public function getVileId(): ?int
    {
        return $this->villeId;
    }

    public function setVilleId(?int $villeId)
    {
        $this->villeId = $villeId;
    }

    public function isCompteActif(): ?bool
    {
        return $this->compteActif;
    }

    public function setCompteActif(?bool $compteActif)
    {
        $this->compteActif = $compteActif;
    }


}
