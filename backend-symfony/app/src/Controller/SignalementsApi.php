<?php
 namespace App\Controller;

use App\Entity\Citoyens;
use App\Service\ConvertAdresse;
 use App\Service\AuthChecker;
 use App\Entity\Signalements;
use App\Entity\Utilisateurs;
use App\Entity\TypesSignalement;
use App\Enum\EtatSignalement;
use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Attribute\Route;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 #[Route('/api/signalements')]
 class SignalementsApi extends AbstractController {
    private EntityManagerInterface $em;
    private ConvertAdresse $convertAdresse;
    private AuthChecker $auth;

    public function __construct(EntityManagerInterface $em, ConvertAdresse $convertAdresse, AuthChecker $auth)
    {
        $this->em = $em;
        $this->convertAdresse = $convertAdresse;
        $this->auth = $auth;
    }

    #[Route('', name: 'api_get_signalement', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);

        if (!$user) {
            return $this->json(["error"=>"Token invalide"],401);
        }

        $userId = $user['userId'];
        $role = $user['role'];

        if ($role === 'admin') {
            $signalement = $this->em
                ->getRepository(Signalements::class)
                ->findAll();
        } else {
            $signalement = $this->em
                ->getRepository(Signalements::class)
                ->createQueryBuilder('s')
                ->where('s.etat != :etat')
                ->orWhere('s.citoyen = :userId')
                ->setParameter('etat', EtatSignalement::ENREGISTRE->value)
                ->setParameter('userId', $userId)
                ->getQuery()
                ->getResult();
        }

        $data = array_map(function($s) use ($userId) {

            $lat = $s->getLatitude();
            $lng = $s->getLongitude();

            $adresse = ($lat && $lng)
                ? $this->convertAdresse->coordinatesToAddress($lat, $lng)
                : null;

            $type = $s->getTypeId()
                ? $this->em->getRepository(TypesSignalement::class)->find($s->getTypeId())
                : null;
            $citoyenId = $s->getCitoyen()?->getUtilisateurId();

            $photo = $s->getPhoto();
            $photoUrl = $photo ? $this->getPhotoUrl($photo) : null;

            return [
                'id' => $s->getId(),
                'titre' => $s->getTitre(),
                'etat' => $s->getEtat()->value,
                'description' => $s->getDescription(),
                'adresse' => $adresse,
                'latitude' => $s->getLatitude(),
                'longitude' => $s->getLongitude(),
                'typeId' => $s->getTypeId(),
                'typeNom' => $type?->getNom(),
                'photo' => $photoUrl,
                'citoyenId' => $citoyenId,
                'auteur?' => $citoyenId === $userId,
                'dateCrea' => $s->getDateCreation(),
                'dateModif' => $s->getDateModification()
            ];

        }, $signalement);

        return $this->json($data);
    }


    #[Route('', name: 'api_post_signalement', methods: ['POST'])]
    public function create(Request $request): JsonResponse{

         $user = $this->auth->getuserfromrequest($request);
        if (!$user) {
            return $this->json(["error" => "token manquant ou invalide"], 401);
        }

        if (!$this->auth->checkrole($user, 'citoyen')) {
            return $this->json(["error" => "accès interdit"], 403);
        }

        // Support à la fois multipart/form-data (avec photo) et JSON pur
        $contentType = $request->headers->get('Content-Type', '');
        if (str_contains($contentType, 'multipart/form-data')) {
            $data = $request->request->all();
            $photoFile = $request->files->get('photo');
        } else {
            $data = json_decode($request->getContent(), true) ?: [];
            $photoFile = null;
        }

         $citoyen = $this->em->getRepository(Citoyens::class)
            ->findOneBy(['utilisateurId' => $user['userId']]);
         if (!$citoyen) {
             return $this->json(["error" => "Citoyen introuvable"], 404);
         }

         $s = new Signalements();
         $s->setTitre($data['titre'] ?? 'Sans titre');
         $s->setEtat(EtatSignalement::ENREGISTRE);
         $s->setDescription($data['description'] ?? null);
         $s->setDateCreation(new \DateTime());
         $s->setDateModification(new \DateTime());
         $s->setCitoyen($citoyen);
         $s->setTypeId(isset($data['typeId']) ? (int) $data['typeId'] : null);

         // Si on a une adresse, on convertit en coordonnées
         if (!empty($data['adresse'])) {
            $coords = $this->convertAdresse->addressToCoordinates($data['adresse']);
            if ($coords !== null) {
                $s->setLatitude($coords['lat']);
                $s->setLongitude($coords['lng']);
            }
        }

         // Sinon si on a déjà lat/lng, on peut setter directement
         elseif (isset($data['latitude'], $data['longitude'])) {
             $s->setLatitude((float) $data['latitude']);
             $s->setLongitude((float) $data['longitude']);
         }

         // Gestion de la photo
         if ($photoFile) {
             $error = $this->validatePhoto($photoFile);
             if ($error) {
                 return $this->json(["error" => $error], 400);
             }
             $filename = uniqid('sig_', true) . '.' . $photoFile->guessExtension();
             $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/signalements';
             if (!is_dir($uploadDir)) {
                 @mkdir($uploadDir, 0775, true);
             }
             $photoFile->move($uploadDir, $filename);
             $s->setPhoto('uploads/signalements/' . $filename);
         }

         $this->em->persist($s);
         $this->em->flush();

         return $this->json([
             'message' => 'Signalement créé',
             'id' => $s->getId(),
             'photo' => $s->getPhoto() ? $this->getPhotoUrl($s->getPhoto()) : null
         ]);
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
        return 'http://localhost:8000/' . ltrim($relativePath, '/');
    }

    #[Route('/{id}', name: 'ChangeEtat', methods: ['GET'])] 
    public function changeEtat(Request $request, int $id): JsonResponse
    {
         $user = $this->auth->getuserfromrequest($request);
        if (!$user) {
            return $this->json(["error" => "token manquant ou invalide"], 401);
        }

        if (!$this->auth->checkrole($user, 'admin')) {
            return $this->json(["error" => "accès interdit"], 403);
        }
        $signalement = $this->em
            ->getRepository(Signalements::class)
            ->find($id);

        if (!$signalement) {
            return $this->json(["error" => "signalement introuvable"],404);
        }

        $etatActuel = $signalement->getEtat();
        $etatSuivant = $etatActuel->next();

        if (!$etatSuivant) {
            return $this->json(["error"=>"déjà résolu"],400);
        }

        $signalement->setEtat($etatSuivant);

        $this->em->flush();

        return $this->json([
            "message"=>"etat modifié",
            "nouvelEtat"=>$etatSuivant->value
        ]);
    }
 }