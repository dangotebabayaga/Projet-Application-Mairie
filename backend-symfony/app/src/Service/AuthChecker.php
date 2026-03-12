<?php
namespace App\Service;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;

class AuthChecker
{
    private string $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'change_me';
    }

    /**
     * Vérifie le token et retourne le payload ou null si invalide
     */
    public function getUserFromRequest(Request $request): ?array
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7); // enlève "Bearer "
        try {
            $payload = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return (array) $payload;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Vérifie que l'utilisateur a le rôle attendu
     */
    public function checkRole(array $payload, string $role): bool
    {
        return isset($payload['role']) && $payload['role'] === $role;
    }
}