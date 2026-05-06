<?php
namespace App\Repository;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class UtilisateurRepository extends ServiceEntityRepository // correction : Utilisateurs → Utilisateur
{
    private EntityManagerInterface $em;
    private VilleRepository $villeRepo;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, VilleRepository $villeRepo)
    {
        parent::__construct($registry, Utilisateur::class);
        $this->em       = $em;
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

        $role = match((int) $data['role']) {
            1 => 'citoyen',
            2 => 'administrateur',
            default => throw new \InvalidArgumentException('Rôle invalide : ' . $data['role']),
        };

        $user = new Utilisateur();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setTelephone($data['telephone'] ?? null);
        $user->setDateCreation(new \DateTime());
        $user->setVille($ville);
        $user->setCompteActif(true);
        $user->setRole($role);

        if (!empty($data['dateNaissance'])) {
            $user->setDateNaissance(new \DateTime($data['dateNaissance']));
        }

        $hash = password_hash($data['motDePasse'], PASSWORD_BCRYPT);
        $user->setMotDePasseHash($hash);
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
            'role'           => $user->getRole(),
        ];
    }

    public function isAdmin(int $userId): bool
    {
        $utilisateur = $this->find($userId); // correction : $utilisateur → $utilisateur
        return $utilisateur !== null && $utilisateur->getRole() === 'administrateur';
    }

    public function isCitoyen(int $userId): bool
    {
        $utilisateur = $this->find($userId); // correction : $utilisateur → $utilisateur
        return $utilisateur !== null && $utilisateur->getRole() === 'citoyen';
    }
}