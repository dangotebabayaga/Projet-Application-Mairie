<?php
namespace App\Controller;

use App\Repository\UtilisateurRepository; // correction : UtilisateurRepository → UtilisateurRepository
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\JwtService;

#[Route('/api/utilisateur')]
class UserApi extends AbstractController
{
    private UtilisateurRepository $userRepo; // correction : UtilisateurRepository → UtilisateurRepository
    private JwtService $jwtService;

    public function __construct(UtilisateurRepository $userRepo, JwtService $jwtService) // correction : idem
    {
        $this->userRepo   = $userRepo;
        $this->jwtService = $jwtService;
    }

    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['motDePasse'])) {
            return $this->json(["error" => "Tous les champs obligatoires doivent être remplis"], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(["error" => "L'adresse email n'est pas valide"], 400);
        }

        $existingUser = $this->userRepo->findByEmail($data['email']);
        if ($existingUser) {
            return $this->json(["error" => "Un compte avec cet email existe déjà"], 409);
        }

        $user = $this->userRepo->createUtilisateur($data);

        return $this->json([
            "message" => "Utilisateurcréé",
            "id"      => $user->getId()
        ]);
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->userRepo->verifierConnexion($data['email'], $data['motDePasse']);
        if (!$user) {
            return $this->json(["error" => "Email ou mot de passe incorrect"], 401);
        }

        $infoUser = $this->userRepo->infoUser($user->getId());

        // correction : getRole($user) → $user->getRole() (méthode directe sur l'entité)
        $token = $this->jwtService->generateToken(
            $user->getId(),
            $user->getEmail(),
            array_map(fn($r) => $r->getNom(), $user->getRoles()->toArray())
        );

        return $this->json([
            "message"  => "Connexion réussie",
            "token"    => $token,
            "infoUser" => $infoUser
        ]);
    }

    public function getUserFromToken(Request $request)
    {
        $header = $request->headers->get('Authorization');
        if (!$header) {
            return null;
        }

        $token = str_replace("Bearer ", "", $header);

        try {
            $decoded = $this->jwtService->decodeToken($token);
            return $this->userRepo->find($decoded->userId);
        } catch (\Exception $e) {
            return null;
        }
    }
    #[Route('/{id}/roles', name: 'api_add_role', methods: ['POST'])]
    public function addRole(int $id, Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'administrateur')) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $data      = json_decode($request->getContent(), true);
        $cible     = $this->userRepo->find($id);
        if (!$cible) {
            return $this->json(["error" => "Utilisateur introuvable"], 404);
        }

        $role = $this->em->getRepository(Role::class)->findOneBy(['nom' => $data['role']]);
        if (!$role) {
            return $this->json(["error" => "Rôle introuvable"], 404);
        }

        $cible->addRole($role);
        $this->em->flush();

        return $this->json(['message' => "Rôle ajouté avec succès"]);
    }

    #[Route('/{id}/roles', name: 'api_remove_role', methods: ['DELETE'])]
    public function removeRole(int $id, Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'administrateur')) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $data  = json_decode($request->getContent(), true);
        $cible = $this->userRepo->find($id);
        if (!$cible) {
            return $this->json(["error" => "Utilisateur introuvable"], 404);
        }

        $role = $this->em->getRepository(Role::class)->findOneBy(['nom' => $data['role']]);
        if (!$role) {
            return $this->json(["error" => "Rôle introuvable"], 404);
        }

        $cible->removeRole($role);
        $this->em->flush();

        return $this->json(['message' => "Rôle retiré avec succès"]);
    }
}