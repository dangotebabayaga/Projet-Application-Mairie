<?php
namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;

class AuthChecker
{
    private string $jwtSecret;
    private UtilisateurRepository $userRepo;

    public function __construct(UtilisateurRepository $userRepo)
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'change_me';
        $this->userRepo  = $userRepo;
    }

    public function getUserFromRequest(Request $request): mixed
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
    
        $token = substr($authHeader, 7);
    
        try {
            $payload = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return $this->userRepo->findByEmail($payload->email); // email est bien présent
        } catch (\Exception $e) {
            return null;
        }
    }

    public function checkRole(mixed $user, string $role): bool
    {
        if ($user === null) return false;
        // correction : utilise getRole() sur l'objet Utilisateur
        return $user->getRole() === $role;
    }
}