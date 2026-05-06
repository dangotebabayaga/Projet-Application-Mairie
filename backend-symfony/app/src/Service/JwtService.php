<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secret;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'];
    }

    // Générer un token
    public function generateToken(int $userId, string $email, string $role): string
    {
        $payload = [
            "userId" => $userId,
            "email" => $email,
            "role" => $role,
            "iat" => time(),
            "exp" => time() + 3600
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    // Vérifier si token valide
    public function validateToken(string $token): bool
    {
        try {
            JWT::decode($token, new Key($this->secret, 'HS256'));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Lire contenu du token
    public function decodeToken(string $token)
    {
        return JWT::decode($token, new Key($this->secret, 'HS256'));
    }
}