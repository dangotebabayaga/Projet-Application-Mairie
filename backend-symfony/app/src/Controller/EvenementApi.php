<?php
namespace App\Controller;

use App\Service\AuthChecker;
use App\Entity\Evenement;
use App\Entity\TypeEv;
use App\Repository\EvenementRepository;
 use App\Repository\AdministrateursRepository;
 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Attribute\Route;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 #[Route('/api/evenement')]
 class EvenementApi extends AbstractController {
    private EntityManagerInterface $em;
    private EvenementRepository $evenRepo;
    private AdministrateursRepository $adminRepo;
    private AuthChecker $auth;

    public function __construct(EntityManagerInterface $em,
    EvenementRepository $evenRepo, AdministrateursRepository $adminRepo,
    AuthChecker $auth)
    {
        $this->em = $em;
        $this->evenRepo=$evenRepo;
        $this->adminRepo=$adminRepo;
        $this->auth=$auth;
    }

    #[Route('', name: 'create_Even', methods: ['POST'])]
    public function create(Request $request): JsonResponse{

         $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }

        if (!$this->auth->checkRole($user, 'admin')) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        // Support multipart/form-data (avec photo) ou JSON pur
        $contentType = $request->headers->get('Content-Type', '');
        if (str_contains($contentType, 'multipart/form-data')) {
            $data = $request->request->all();
            $photoFile = $request->files->get('photo');
        } else {
            $data = json_decode($request->getContent(), true) ?: [];
            $photoFile = null;
        }

         $ev = $this->evenRepo->crea($data);

         // Upload photo si présent
         if ($photoFile) {
             $error = $this->validatePhoto($photoFile);
             if ($error) {
                 return $this->json(["error" => $error], 400);
             }
             $filename = uniqid('ev_', true) . '.' . $photoFile->guessExtension();
             $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/evenements';
             if (!is_dir($uploadDir)) {
                 @mkdir($uploadDir, 0775, true);
             }
             $photoFile->move($uploadDir, $filename);
             $ev->setPhoto('uploads/evenements/' . $filename);
         }

         $this->em->persist($ev);
         $this->em->flush();

         return $this->json([
             'message' => 'Événement créé',
             'id' => $ev->getId(),
             'photo' => $ev->getPhoto() ? $this->getPhotoUrl($ev->getPhoto()) : null
         ]);
    }

    #[Route('', name: 'get_all_Even', methods: ['GET'])]
    public function getall(Request $request): JsonResponse
    {
         $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }

        $even = $this->em->getRepository(Evenement::class)->findAll();

        $data = [];

        foreach ($even as $e) {

            $type = $this->em->getRepository(TypeEv::class)->find($e->getType());
            $photo = $e->getPhoto();

            $data[] = [
                "id" => $e->getId(),
                "titre" => $e->getTitre(),
                "lieux" => $e->getLieux(),
                "commentaire" => $e->getCommentaire(),
                "date Evenement" => $e->getDateEv()?->format('Y-m-d'),
                "Heure début" => $e->getHeureDeb()?->format('H:i'),
                "Heure fin" => $e->getHeureFin()?->format('H:i'),
                "adminId" => $e->getAdministrateurId(),
                "type" => $type?->getNom(),
                "photo" => $photo ? $this->getPhotoUrl($photo) : null
            ];
        }

        return $this->json($data);
    }
    #[Route('/listeType', name: 'get_all_TypeEven', methods: ['GET'])]
    public function getallType(Request $request): JsonResponse
    {
         $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        $type = $this->em->getRepository(TypeEv::class)->findAll();
        $data = [];

        foreach ($type as $e) {

            $data[] = [
                "type" => $e->getNom()
            ];
        }

        return $this->json($data);
    }

    private function validatePhoto($file): ?string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowed, true)) {
            return 'Format non autorisé (JPEG, PNG, WEBP ou GIF uniquement)';
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return 'Fichier trop gros (max 5 Mo)';
        }
        return null;
    }

    private function getPhotoUrl(string $relativePath): string
    {
        $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:8000';
        return rtrim($baseUrl, '/') . '/' . ltrim($relativePath, '/');
    }

    #[Route('/{id}', name: 'update_Even', methods: ['PUT', 'POST'], requirements: ['id' => '\d+'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'admin')) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $ev = $this->evenRepo->find($id);
        if (!$ev) {
            return $this->json(["error" => "Événement introuvable"], 404);
        }

        // Support multipart (avec photo) ou JSON
        $contentType = $request->headers->get('Content-Type', '');
        if (str_contains($contentType, 'multipart/form-data')) {
            $data = $request->request->all();
            $photoFile = $request->files->get('photo');
        } else {
            $data = json_decode($request->getContent(), true) ?: [];
            $photoFile = null;
        }

        $this->evenRepo->maj($ev, $data);

        if ($photoFile) {
            $error = $this->validatePhoto($photoFile);
            if ($error) {
                return $this->json(["error" => $error], 400);
            }
            $filename = uniqid('ev_', true) . '.' . $photoFile->guessExtension();
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/evenements';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0775, true);
            }
            $photoFile->move($uploadDir, $filename);
            $ev->setPhoto('uploads/evenements/' . $filename);
        }

        $this->em->flush();

        return $this->json([
            'message' => 'Événement mis à jour',
            'id' => $ev->getId(),
            'photo' => $ev->getPhoto() ? $this->getPhotoUrl($ev->getPhoto()) : null
        ]);
    }

 }
