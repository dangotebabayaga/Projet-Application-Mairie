<?php
namespace App\Repository;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Entity\Role;
use App\Repository\VilleRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class UtilisateurRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    private VilleRepository $villeRepo;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, VilleRepository $villeRepo)
    {
        parent::__construct($registry, Utilisateur::class);
        $this->em        = $em;
        $this->villeRepo = $villeRepo;
    }

    public function findByEmail(string $email): ?Utilisateur
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function verifierConnexion(string $email, string $motDePasse): ?Utilisateur
    {
        $utilisateur = $this->findByEmail($email);
        if (!$utilisateur) return null;
        if (password_verify($motDePasse, $utilisateur->getMotDePasseHash())) {
            return $utilisateur;
        }
        return null;
    }

    public function createUtilisateur(array $data): Utilisateur
    {
        $ville = $this->villeRepo->findOneBy([]);
        if (!$ville) {
            $ville = new Ville();
            $ville->setNom('Ville par défaut');
            $ville->setSlogan('Slogan par défaut');
            $ville->setLogo(null);
            $ville->setThemeCouleur(null);
            $ville->setDateCreation(new \DateTime());
            $this->em->persist($ville);
            $this->em->flush();
        }

        // correction : tableau de rôles au lieu d'un seul rôle
        $rolesNoms = $data['role'] ?? [];

        // compatibilité avec l'ancien champ 'role' (int)
        if (empty($rolesNoms) && isset($data['role'])) {
            $roleVal = $data['role'];

            // si role est une string JSON
            if (is_string($roleVal) && str_starts_with($roleVal, '[')) {
                $rolesNoms = json_decode($roleVal, true) ?? [];
            }
            // si c'est une string directe 'elu'
            elseif (is_string($roleVal) && !is_numeric($roleVal)) {
                $rolesNoms = [$roleVal];
            }
            // si c'est un entier (ancienne API)
            else {
                $rolesNoms = match((int) $roleVal) {
                    1 => ['citoyen'],
                    2 => ['elu'],
                    3 => ['administrateur'],
                    default => throw new \InvalidArgumentException('Rôle invalide : ' . $roleVal),
                };
            }
        }

        if (empty($rolesNoms)) {
            throw new \InvalidArgumentException('Au moins un rôle est requis');
        }

        $user = new Utilisateur();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setTelephone($data['telephone'] ?? null);
        $user->setDateCreation(new \DateTime());
        $user->setVille($ville);
        $user->setCompteActif(true);

        if (!empty($data['dateNaissance'])) {
            $user->setDateNaissance(new \DateTime($data['dateNaissance']));
        }

        $hash = password_hash($data['motDePasse'], PASSWORD_BCRYPT);
        $user->setMotDePasseHash($hash);

        // correction : ajout des rôles via la table de liaison
        foreach ($rolesNoms as $nomRole) {
            $role = $this->em->getRepository(Role::class)->findOneBy(['nom' => $nomRole]);
            if (!$role) {
                throw new \InvalidArgumentException('Rôle introuvable : ' . $nomRole);
            }
            $user->addRole($role);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function infoUser(int $id): array
    {
        $user = $this->find($id);
        return [
            'id'             => $user->getId(),
            'nom'            => $user->getNom(),
            'prenom'         => $user->getPrenom(),
            'email'          => $user->getEmail(),
            'telephone'      => $user->getTelephone(),
            'date Naissance' => $user->getDateNaissance(),
            'role'          => array_map(fn($r) => $r->getNom(), $user->getRoles()->toArray()), // correction : getRole() → getRoles()
        ];
    }

    public function isadministrateur(int $userId): bool
    {
        $utilisateur = $this->find($userId);
        return $utilisateur !== null && $utilisateur->hasRole('administrateur'); // correction : getRole() → hasRole()
    }

    public function isCitoyen(int $userId): bool
    {
        $utilisateur = $this->find($userId);
        return $utilisateur !== null && $utilisateur->hasRole('citoyen'); // correction : getRole() → hasRole()
    }
}