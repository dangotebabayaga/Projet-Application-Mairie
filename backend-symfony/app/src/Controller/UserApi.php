<?php
namespace App\Controller;

use App\Repository\UtilisateursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/utilisateur')]
class UserApi extends AbstractController
{

    private UtilisateursRepository $userRepo;

    public function __construct(UtilisateursRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    #[Route('/register', methods:['POST'])]
    public function register(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $user = $this->userRepo->createUtilisateur($data);

        return $this->json([
            "message" => "Utilisateur créé",
            "id" => $user->getId()
        ]);
    }

    #[Route('/login', methods:['POST'])]
    public function login(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $user = $this->userRepo->verifierConnexion(
            $data['email'],
            $data['motDePasse']
        );

        if (!$user) {
            return $this->json([
                "error" => "Email ou mot de passe incorrect"
            ],401);
        }

        return $this->json([
            "message" => "Connexion réussie",
            "id" => $user->getId(),
            "email" => $user->getEmail()
        ]);
    }

    #[Route('/login', name:'api_login', methods:['POST'])]
    public function verifierConnexion(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['motDePasse'])) {
            return $this->json([
                "error" => "Email ou mot de passe manquant"
            ],400);
        }
        $utilisateur = $this->userRepo->verifierConnexion($data['email'], $data['motDePasse']);

        if (!$utilisateur) {
            return $this->json(['error' => 'Email ou mot de passe incorrect'], 401);
        }

        return $this->json(['message' => 'Connexion réussie', 'id' => $utilisateur->getUtilisateurId()]);

    }
}