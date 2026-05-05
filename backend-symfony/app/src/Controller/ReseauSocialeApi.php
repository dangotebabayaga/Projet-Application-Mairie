<?php
namespace App\Controller;

use App\Service\AuthChecker;
use App\Entity\ReseauSociale;
use App\Repository\ReseauSocialeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/reseau')]
class ReseauSocialeApi extends AbstractController
{
    private EntityManagerInterface $em;
    private ReseauSocialeRepository $repo;
    private AuthChecker $auth;

    public function __construct(EntityManagerInterface $em, ReseauSocialeRepository $repo, AuthChecker $auth)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->auth = $auth;
    }

    #[Route('', name: 'get_all_reseau', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $reseaux = $this->repo->findAll();
        $data = [];
        foreach ($reseaux as $r) {
            $data[] = [
                'id' => $r->getId(),
                'villeId' => $r->getVilleId(),
                'plateform' => $r->getPlateform(),
                'lien' => $r->getLien()
            ];
        }
        return $this->json($data);
    }

    #[Route('', name: 'create_reseau', methods: ['POST'])]
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
        if (empty($data['plateform']) || empty($data['lien'])) {
            return $this->json(["error" => "Plateforme et lien obligatoires"], 400);
        }

        $r = new ReseauSociale();
        $r->setVilleId($data['villeId'] ?? 1);
        $r->setPlateform($data['plateform']);
        $r->setLien($data['lien']);

        $this->em->persist($r);
        $this->em->flush();

        return $this->json(['message' => 'Réseau social créé', 'id' => $r->getId()]);
    }

    #[Route('/{id}', name: 'update_reseau', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'admin')) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $r = $this->repo->find($id);
        if (!$r) {
            return $this->json(["error" => "Réseau introuvable"], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['plateform'])) $r->setPlateform($data['plateform']);
        if (isset($data['lien'])) $r->setLien($data['lien']);

        $this->em->flush();

        return $this->json(['message' => 'Réseau social modifié']);
    }

    #[Route('/{id}', name: 'delete_reseau', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'admin')) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $r = $this->repo->find($id);
        if (!$r) {
            return $this->json(["error" => "Réseau introuvable"], 404);
        }

        $this->em->remove($r);
        $this->em->flush();

        return $this->json(['message' => 'Réseau social supprimé']);
    }
}
