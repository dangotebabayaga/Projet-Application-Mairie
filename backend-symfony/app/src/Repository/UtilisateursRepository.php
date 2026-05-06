<?php
namespace App\Repository;

use App\Entity\Utilisateurs;
use App\Entity\Citoyens;
use App\Entity\Admin;
use App\Entity\SuperAdministrateur;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Symfony\Component\HttpFoundation\JsonResponse;

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
            $this->em->flush();

            // Attribution quartier / catégorie via SQL natif
            $quartierId = isset($data['quartierId']) && $data['quartierId'] !== '' ? (int) $data['quartierId'] : null;
            $categorieId = isset($data['categorieId']) && $data['categorieId'] !== '' ? (int) $data['categorieId'] : null;
            if ($quartierId !== null || $categorieId !== null) {
                $this->em->getConnection()->executeStatement(
                    'UPDATE citoyens SET quartier_id = :q, categorie_id = :c WHERE utilisateur_id = :u',
                    ['q' => $quartierId, 'c' => $categorieId, 'u' => $user->getId()]
                );
            }
        } elseif ($data['role'] == 2) {

            $admin = new Admin();
            $admin->setUtilisateurId($user->getId());
            $this->em->persist($admin);
        }

        $this->em->flush();

        return $user;
    }

    public function updateUtilisateur(Utilisateurs $user, array $data): void
    {
        if (array_key_exists('nom', $data) && $data['nom'] !== null) {
            $user->setNom($data['nom']);
        }
        if (array_key_exists('prenom', $data) && $data['prenom'] !== null) {
            $user->setPrenom($data['prenom']);
        }
        if (array_key_exists('email', $data) && $data['email'] !== null) {
            $user->setEmail($data['email']);
        }
        if (array_key_exists('telephone', $data)) {
            $user->setTelephone($data['telephone'] ?: null);
        }
        if (array_key_exists('dateNaissance', $data)) {
            $user->setDateNaissance(
                $data['dateNaissance'] ? new \DateTime($data['dateNaissance']) : null
            );
        }
        $this->em->flush();
    }

    public function infoUser(int $id): array{
        $user=$this->em->getRepository(Utilisateurs::class)->findOneBy(['id'=>$id]);
        $role = $this->getRole($user);

        $quartierId = null;
        $categorieId = null;
        if ($role === 'citoyen') {
            $row = $this->em->getConnection()->fetchAssociative(
                'SELECT quartier_id, categorie_id FROM citoyens WHERE utilisateur_id = :id',
                ['id' => $id]
            );
            if ($row) {
                $quartierId = $row['quartier_id'] !== null ? (int) $row['quartier_id'] : null;
                $categorieId = $row['categorie_id'] !== null ? (int) $row['categorie_id'] : null;
            }
        }

        return [
            "id" => $user->getId(),
            "nom"=>$user->getNom(),
            "prenom"=>$user->getPrenom(),
            "email" => $user->getEmail(),
            "telephonne" => $user->getTelephone(),
            "date Naissance"=> $user->getDateNaissance(),
            "role" => $role,
            "villeId" => $user->getVilleId(),
            "quartierId" => $quartierId,
            "categorieId" => $categorieId
        ];
    }

    /**
     * Met à jour quartier et/ou catégorie pour un citoyen donné.
     * Champs absents de $data = pas de changement.
     */
    public function updateCitoyenAffectation(int $userId, array $data): void
    {
        $hasQuartier = array_key_exists('quartierId', $data);
        $hasCategorie = array_key_exists('categorieId', $data);
        if (!$hasQuartier && !$hasCategorie) return;

        $sets = [];
        $params = ['u' => $userId];
        if ($hasQuartier) {
            $sets[] = 'quartier_id = :q';
            $params['q'] = $data['quartierId'] === null || $data['quartierId'] === '' ? null : (int) $data['quartierId'];
        }
        if ($hasCategorie) {
            $sets[] = 'categorie_id = :c';
            $params['c'] = $data['categorieId'] === null || $data['categorieId'] === '' ? null : (int) $data['categorieId'];
        }

        $this->em->getConnection()->executeStatement(
            'UPDATE citoyens SET ' . implode(', ', $sets) . ' WHERE utilisateur_id = :u',
            $params
        );
    }

    public function getRole(Utilisateurs $user): string
    {
        $em = $this->getEntityManager();

        // Priorité : superadmin > admin > citoyen
        $isSuperAdmin = $em->getRepository(SuperAdministrateur::class)
                           ->find($user->getId());

        if ($isSuperAdmin) return 'superadmin';

        $isAdmin = $em->getRepository(Admin::class)
                      ->find($user->getId());

        if ($isAdmin) return 'admin';

        $isCitoyen = $em->getRepository(Citoyens::class)
                         ->find($user->getId());

        if ($isCitoyen) return 'citoyen';

        return 'unknown';
    }
}