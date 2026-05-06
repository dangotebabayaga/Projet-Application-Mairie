<?php
namespace App\Controller;

use App\Entity\Quartier;
use App\Repository\QuartierRepository;
use App\Service\AuthChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/quartiers')]
class QuartierApi extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private QuartierRepository $repo,
        private AuthChecker $auth
    ) {}

    /**
     * Liste des quartiers de la ville (par défaut id=1, mono-ville).
     * Public - alimente les selects à l'inscription.
     */
    #[Route('', name: 'list_quartiers', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $villeId = (int) ($request->query->get('villeId') ?? 1);
        $items = $this->repo->findByVille($villeId);
        return $this->json(array_map(fn(Quartier $q) => [
            'id' => $q->getId(),
            'nom' => $q->getNom(),
            'villeId' => $q->getVilleId()
        ], $items));
    }

    #[Route('', name: 'create_quartier', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $err = $this->requireAdmin($request);
        if ($err) return $err;

        $data = json_decode($request->getContent(), true) ?: [];
        if (empty($data['nom'])) {
            return $this->json(['error' => 'Nom obligatoire'], 400);
        }

        $q = new Quartier();
        $q->setNom(trim($data['nom']));
        $q->setVilleId((int) ($data['villeId'] ?? 1));

        $this->em->persist($q);
        $this->em->flush();

        return $this->json(['message' => 'Quartier créé', 'id' => $q->getId()]);
    }

    #[Route('/{id}', name: 'update_quartier', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $err = $this->requireAdmin($request);
        if ($err) return $err;

        $q = $this->repo->find($id);
        if (!$q) {
            return $this->json(['error' => 'Quartier introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true) ?: [];
        if (!empty($data['nom'])) $q->setNom(trim($data['nom']));

        $this->em->flush();
        return $this->json(['message' => 'Quartier mis à jour']);
    }

    #[Route('/{id}', name: 'delete_quartier', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        $err = $this->requireAdmin($request);
        if ($err) return $err;

        $q = $this->repo->find($id);
        if (!$q) {
            return $this->json(['error' => 'Quartier introuvable'], 404);
        }

        $this->em->remove($q);
        $this->em->flush();
        return $this->json(['message' => 'Quartier supprimé']);
    }

    private function requireAdmin(Request $request): ?JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(['error' => 'Token manquant ou invalide'], 401);
        }
        if (!$this->auth->checkRole($user, 'admin')) {
            return $this->json(['error' => 'Accès interdit'], 403);
        }
        return null;
    }
}
