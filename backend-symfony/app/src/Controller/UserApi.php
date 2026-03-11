<?php
namespace App\Controller;

use App\Repository\UtilisateursRepository;
use App\Repository\AdministrateursRepository;
use App\Repository\CitoyensRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/utilisateur')]
class UserApi extends AbstractController
{

    private UtilisateursRepository $userRepo;
    private AdministrateursRepository $adminRepo;
    private CitoyensRepository $citoyenRepo;

    public function __construct(
        UtilisateursRepository $userRepo,
        AdministrateursRepository $adminRepo,
        CitoyensRepository $citoyenRepo
    ) {
        $this->userRepo = $userRepo;
        $this->adminRepo = $adminRepo;
        $this->citoyenRepo = $citoyenRepo;
    }

    #[Route('/register', methods:['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['motDePasse'])) {
            return $this->json([
                "error" => "Tous les champs obligatoires doivent être remplis"
            ], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                "error" => "L'adresse email n'est pas valide"
            ], 400);
        }

        // Vérifier si l'email existe déjà
        $existingUser = $this->userRepo->findByEmail($data['email']);
        if ($existingUser) {
            return $this->json([
                "error" => "Un compte avec cet email existe déjà"
            ], 409);
        }

        $user = $this->userRepo->createUtilisateur($data);

        return $this->json([
            "message" => "Utilisateur créé",
            "id" => $user->getId()
        ]);
    }

    #[Route('/login', name:'api_login', methods:['POST'])]
    public function login(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['motDePasse'])) {
            return $this->json([
                "error" => "Email ou mot de passe manquant"
            ], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                "error" => "L'adresse email n'est pas valide"
            ], 400);
        }

        $user = $this->userRepo->verifierConnexion($data['email'], $data['motDePasse']);

        if (!$user) {
            return $this->json([
                "error" => "Email ou mot de passe incorrect"
            ], 401);
        }

        // Déterminer le rôle
        $role = 'citoyen';
        if ($this->adminRepo->isAdmin($user->getId())) {
            $role = 'admin';
        }

        return $this->json([
            "message" => "Connexion réussie",
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "prenom" => $user->getPrenom(),
            "role" => $role,
            "villeId" => $user->getVileId()
        ]);
    }

}