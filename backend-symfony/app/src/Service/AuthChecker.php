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
            return $this->userRepo->findByEmail($payload->email);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Vérifie un seul rôle
    public function checkRole(mixed $user, string $role): bool
    {
        if ($user === null) return false;
        return $user->hasRole($role);
    }

    // Vérifie si l'utilisateur a au moins un des rôles donnés
    public function checkAnyRole(mixed $user, array $roles): bool
    {
        if ($user === null) return false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) return true;
        }
        return false;
    }
}