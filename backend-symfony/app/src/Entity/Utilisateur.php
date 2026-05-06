<?php
namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(
        name: 'utilisateur_roles',
        joinColumns: [new ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')]
    )]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function hasRole(string $nomRole): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getNom() === $nomRole) {
                return true;
            }
        }
        return false;
    }

    public function addRole(Role $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    public function removeRole(Role $role): void
    {
        $this->roles->removeElement($role);
    }
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

}