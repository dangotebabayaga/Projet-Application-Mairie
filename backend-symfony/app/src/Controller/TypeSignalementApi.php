<?php
namespace App\Controller;

use App\Service\AuthChecker;
use App\Entity\TypesSignalement;
use App\Repository\TypesSignalementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/types-signalement')]
class TypeSignalementApi extends AbstractController
{
    private EntityManagerInterface $em;
    private TypesSignalementRepository $repo;
    private AuthChecker $auth;

    public function __construct(EntityManagerInterface $em, TypesSignalementRepository $repo, AuthChecker $auth)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->auth = $auth;
    }

    #[Route('', name: 'get_all_types_signalement', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $types = $this->repo->findAll();
        $data = array_map(fn($t) => [
            'id' => $t->getId(),
            'nom' => $t->getNom()
        ], $types);
        return $this->json($data);
    }

    #[Route('', name: 'create_type_signalement', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'admin')) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (empty($data['nom'])) {
            return $this->json(["error" => "Nom obligatoire"], 400);
        }

        $t = new TypesSignalement();
        $t->setNom(trim($data['nom']));

        $this->em->persist($t);
        $this->em->flush();

        return $this->json(['message' => 'Type créé', 'id' => $t->getId()]);
    }

    #[Route('/{id}', name: 'delete_type_signalement', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'admin')) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $t = $this->repo->find($id);
        if (!$t) {
            return $this->json(["error" => "Type introuvable"], 404);
        }

        $this->em->remove($t);
        $this->em->flush();

        return $this->json(['message' => 'Type supprimé']);
    }
}
