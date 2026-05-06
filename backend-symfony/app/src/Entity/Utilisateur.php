<?php
namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'Utilisateurs')]
class Utilisateur // correction : 'Utilisateur' → 'Utilisateur'
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'mot_de_passe_hash', length: 255, nullable: true)]
    private ?string $motDePasseHash = null;

    #[ORM\Column(type: 'datetime', name: 'date_creation', nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'date', name: 'date_naissance', nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    // correction : id brut → relation ManyToOne vers Ville
    #[ORM\ManyToOne(targetEntity: Ville::class)]
    #[ORM\JoinColumn(name: 'ville_id', referencedColumnName: 'id', nullable: true)]
    private ?Ville $ville = null;

    #[ORM\Column(name: 'compte_actif', nullable: true)]
    private ?bool $compteActif = null;

    // correction : champ role ajouté (remplace les anciennes tables administrateurs/citoyens)
    #[ORM\Column(length: 20, nullable: false, options: ['default' => 'citoyen'])]
    private string $role = 'citoyen';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getMotDePasseHash(): ?string
    {
        return $this->motDePasseHash;
    }

    public function setMotDePasseHash(?string $motDePasseHash): void
    {
        $this->motDePasseHash = $motDePasseHash;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    // correction : getVileId (typo) + ?int → ?Ville
    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): void
    {
        $this->ville = $ville;
    }

    public function isCompteActif(): ?bool
    {
        return $this->compteActif;
    }

    public function setCompteActif(?bool $compteActif): void
    {
        $this->compteActif = $compteActif;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        if (!in_array($role, ['citoyen', 'administrateur'])) {
            throw new \InvalidArgumentException('Rôle invalide : ' . $role);
        }
        $this->role = $role;
    }
}