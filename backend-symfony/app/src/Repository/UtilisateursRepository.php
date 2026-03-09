<?php
namespace App\Repository;

use App\Entity\Utilisateurs;
use App\Entity\Citoyens;
use App\Entity\Admin;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class UtilisateursRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    private VilleRepository $villeRepo;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, VilleRepository $villeRepo)
    {
        parent::__construct($registry, Utilisateurs::class);
        $this->em = $em;
        $this->villeRepo=$villeRepo;
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
        $ville = $this->villeRepo->findOneBy([]);
    
        if (!$ville) {
            $ville = new Ville();
            $ville->setNom('Ville par défaut');
            $ville->setSlogan('Slogan par défaut');
            $ville->setLogo(null);
            $ville->setThemeCouleur(1);
            $ville->setDateCreation(new \DateTime());
        
            $this->em->persist($ville);
            $this->em->flush();
        }
    
        $user = new Utilisateurs();
    
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setTelephone($data['telephone'] ?? null);
        $user->setDateCreation(new \DateTime());
        $user->setVilleId($ville->getId());
        $user->setCompteActif(true);
    
        if (!empty($data['dateNaissance'])) {
            $user->setDateNaissance(new \DateTime($data['dateNaissance']));
        }
    
        $hash = password_hash($data['motDePasse'], PASSWORD_BCRYPT);
        $user->setMotDePasseHash($hash);
    
        $this->em->persist($user);
        $this->em->flush();
    
        if ($data['role'] == 1) {
        
            $citoyen = new Citoyens();
            $citoyen->setUtilisateurId($user->getId());
            $this->em->persist($citoyen);
        
        } elseif ($data['role'] == 2) {
        
            $admin = new Admin();
            $admin->setUtilisateurId($user->getId());
            $this->em->persist($admin);
        
        }
    
        $this->em->flush();
    
        return $user;
    }
}