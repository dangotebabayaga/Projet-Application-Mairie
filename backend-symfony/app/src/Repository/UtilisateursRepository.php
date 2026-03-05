<?php
namespace App\Repository;

use App\Entity\Utilisateurs;
use App\Entity\Citoyens;
use App\Entity\Administrateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class UtilisateursRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Utilisateurs::class);
        $this->em = $em;
    }

    public function findByEmail(string $email): ?Utilisateurs
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function verifierConnexion(string $email, string $motDePasse): ?Utilisateurs
    {
        $utilisateur = $this->findOneBy(['email' => $email]);
        
        if (!$utilisateur) {
            return null;
        }

        // Vérifie le mot de passe hashé
        if (password_verify($motDePasse, $utilisateur->getMotDePasseHash())) {
            return $utilisateur;
        }

        return null;
    }

    public function createUtilisateur(array $data): Utilisateurs
    {
        $user = new Utilisateurs();

        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setTelephone($data['telephone'] ?? null);
        $user->setDateCreation(new \DateTime());
        $user->setCompteActif(true);

        if (!empty($data['dateNaissance'])) {
            $user->setDateNaissance(new \DateTime($data['dateNaissance']));
        }

        // hash mot de passe
        $hash = password_hash($data['motDePasse'], PASSWORD_BCRYPT);
        $user->setMotDePasseHash($hash);

        $this->em->persist($user);
        $this->em->flush();

        // gestion du rôle
        if ($data['role'] == 1) {

            $citoyen = new Citoyens();
            $citoyen->setUtilisateurId($user->getId());
            $this->em->persist($citoyen);

        } elseif ($data['role'] == 2) {

            $admin = new Administrateurs();
            $admin->setUtilisateurId($user->getId());
            $admin->setVilleId(1);
            $this->em->persist($admin);

        }

        $this->em->flush();

        return $user;
    }
}