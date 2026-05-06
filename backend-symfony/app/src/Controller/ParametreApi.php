<?php
namespace App\Controller;

use App\Service\AuthChecker;
use App\Entity\Ville;
use App\Repository\UtilisateurRepository; // correction : AdministrateursRepository → UtilisateurRepository
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/paramettre')]
class ParametreApi extends AbstractController
{
    private EntityManagerInterface $em;
    private UtilisateurRepository $userRepo; // correction : adminRepo → userRepo
    private AuthChecker $auth;

    public function __construct(
        EntityManagerInterface $em,
        UtilisateurRepository $userRepo, // correction : AdministrateursRepository → UtilisateurRepository
        AuthChecker $auth
    ) {
        $this->em       = $em;
        $this->userRepo = $userRepo;
        $this->auth     = $auth;
    }

   #[Route('/{id}/theme', name: 'get_theme', methods: ['GET'])]
    public function getTheme(Request $request, int $id): JsonResponse
    {
        $ville = $this->em->getRepository(Ville::class)->findOneBy(['id' => $id]);
        if (!$ville) {
            return $this->json(["error" => "Ville introuvable"], 404);
        }

        return $this->json([
            'slogan' => $ville->getSlogan(),
            'logo'   => $ville->getLogo(),
            'theme'  => $ville->getThemeCouleur()
        ]);
    } 
    #[Route('', name: 'updateVille', methods: ['POST'])]
    public function updateVille(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request); // correction : getuserfromrequest → getUserFromRequest
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'administrateur')) { // correction : checkrole/'admin' → checkRole/'administrateur'
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $data  = json_decode($request->getContent(), true);
        $ville = $this->em->getRepository(Ville::class)->findOneBy(['id' => $data['id']]);
        if (!$ville) {
            return $this->json(["error" => "Ville introuvable"], 404);
        }

        $ville->setLogo($data['logo'] ?? $ville->getLogo());
        $ville->setNom($data['nom'] ?? $ville->getNom());
        $ville->setSlogan($data['slogan'] ?? $ville->getSlogan());
        $ville->setThemeCouleur($data['theme'] ?? $ville->getThemeCouleur()); // correction : setThemecouleur → setThemeCouleur

        $this->em->flush();

        return $this->json(['message' => "La ville a été modifiée"]);
    }

  #[Route('/{id}/info', name: 'get_info', methods: ['GET'])]
    public function getInfo(Request $request, int $id): JsonResponse
    {
        $ville = $this->em->getRepository(Ville::class)->findOneBy(['id' => $id]);
        if (!$ville) {
            return $this->json(["error" => "Ville introuvable"], 404);
        }
    
        return $this->json([
            'id'       => $ville->getId(),
            'nom'      => $ville->getNom(),
            'slogan'   => $ville->getSlogan(),
            'logo'     => $ville->getLogo(),
            'theme'    => $ville->getThemeCouleur(),
            'dateCrea' => $ville->getDateCreation()?->format('Y-m-d H:i:s')
        ]);
    } 
}