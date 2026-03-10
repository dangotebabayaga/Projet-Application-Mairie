<?php
namespace App\Service;

use App\Repository\UtilisateursRepository;
use App\Entity\Utilisateurs;
use Symfony\Component\HttpFoundation\Request;

class AuthChecker
{
    private UtilisateursRepository $userRepo;

    public function __construct(UtilisateursRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getUser(Request $request): ?Utilisateurs
    {
        $userId = $request->headers->get('X-User-Id');
        if (!$userId) {
            return null;
        }
        return $this->userRepo->find((int) $userId);
    }
}
