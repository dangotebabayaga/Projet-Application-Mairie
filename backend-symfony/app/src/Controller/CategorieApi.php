<?php
namespace App\Controller;

use App\Entity\CategorieCitoyen;
use App\Repository\CategorieCitoyenRepository;
use App\Service\AuthChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories')]
class CategorieApi extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CategorieCitoyenRepository $repo,
        private AuthChecker $auth
    ) {}

    #[Route('', name: 'list_categories', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $villeId = (int) ($request->query->get('villeId') ?? 1);
        $items = $this->repo->findByVille($villeId);
        return $this->json(array_map(fn(CategorieCitoyen $c) => [
            'id' => $c->getId(),
            'libelle' => $c->getLibelle(),
            'villeId' => $c->getVilleId()
        ], $items));
    }

    #[Route('', name: 'create_categorie', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $err = $this->requireAdmin($request);
        if ($err) return $err;

        $data = json_decode($request->getContent(), true) ?: [];
        if (empty($data['libelle'])) {
            return $this->json(['error' => 'Libellé obligatoire'], 400);
        }

        $c = new CategorieCitoyen();
        $c->setLibelle(trim($data['libelle']));
        $c->setVilleId((int) ($data['villeId'] ?? 1));

        $this->em->persist($c);
        $this->em->flush();

        return $this->json(['message' => 'Catégorie créée', 'id' => $c->getId()]);
    }

    #[Route('/{id}', name: 'update_categorie', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $err = $this->requireAdmin($request);
        if ($err) return $err;

        $c = $this->repo->find($id);
        if (!$c) {
            return $this->json(['error' => 'Catégorie introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true) ?: [];
        if (!empty($data['libelle'])) $c->setLibelle(trim($data['libelle']));

        $this->em->flush();
        return $this->json(['message' => 'Catégorie mise à jour']);
    }

    #[Route('/{id}', name: 'delete_categorie', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        $err = $this->requireAdmin($request);
        if ($err) return $err;

        $c = $this->repo->find($id);
        if (!$c) {
            return $this->json(['error' => 'Catégorie introuvable'], 404);
        }

        $this->em->remove($c);
        $this->em->flush();
        return $this->json(['message' => 'Catégorie supprimée']);
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
