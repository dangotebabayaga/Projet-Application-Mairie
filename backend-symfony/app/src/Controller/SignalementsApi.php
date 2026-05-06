<?php
namespace App\Controller;

use App\Service\ConvertAdresse;
use App\Service\AuthChecker;
use App\Entity\Signalements;
use App\Enum\EtatSignalement;
use App\Entity\TypesSignalement;
use App\Entity\Utilisateur;
use App\Repository\SignalementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/signalements')]
class SignalementsApi extends AbstractController
{
    private EntityManagerInterface $em;
    private ConvertAdresse $convertAdresse;
    private AuthChecker $auth;

    public function __construct(
        EntityManagerInterface $em,
        ConvertAdresse $convertAdresse,
        AuthChecker $auth
    ) {
        $this->em             = $em;
        $this->convertAdresse = $convertAdresse;
        $this->auth           = $auth;
    }

    #[Route('', name: 'api_get_signalement', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }

        $signalements = $this->em->getRepository(Signalements::class)->findAll();

        $data = array_map(function ($s) {
            $lat = $s->getLatitude() ?? null;
            $lng = $s->getLongitude() ?? null;

            $adresse = ($lat !== null && $lng !== null)
                ? $this->convertAdresse->coordinatesToAddress($lat, $lng)
                : null;

            return [
                'id'          => $s->getId(),
                'titre'       => $s->getTitre(),
                'etat'        => $s->getEtat(),
                'description' => $s->getDescription(),
                'adresse'     => $adresse,
                'typeId'      => $s->getType()?->getId(),
                'type'        => $s->getType()?->getNom(),
                'utilisateurId'   => $s->getUtilisateur()?->getId(),
                'dateCrea'    => $s->getDateCreation()?->format('Y-m-d H:i:s'),
                'dateModif'   => $s->getDateModification()?->format('Y-m-d H:i:s')
            ];
        }, $signalements);

        return $this->json($data);
    }

    #[Route('', name: 'api_post_signalement', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkAnyRole($user, ['citoyen', 'administrateur'])) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $data      = json_decode($request->getContent(), true);
        $dateCrea  = isset($data['dateCrea'])  ? new \DateTime($data['dateCrea'])  : new \DateTime();
        $dateModif = isset($data['dateModif']) ? new \DateTime($data['dateModif']) : new \DateTime();

        $type = null;
        if (!empty($data['type'])) {
            $type = $this->em->getRepository(TypesSignalement::class)->findOneBy(['nom' => $data['type']]);
            if (!$type) {
                $type = new TypesSignalement();
                $type->setNom($data['type']);
                $this->em->persist($type);
            }
        }

        $s = new Signalements();
        $s->setTitre($data['titre'] ?? 'Sans titre');
        $s->setEtat(EtatSignalement::ENREGISTRE);
        $s->setDescription($data['description'] ?? null);
        $s->setDateCreation($dateCrea);
        $s->setDateModification($dateModif);
        $s->setType($type);
        $s->setUtilisateur($user);

        if (!empty($data['adresse'])) {
            $coords = $this->convertAdresse->addressToCoordinates($data['adresse']);
            if ($coords !== null) {
                $s->setLatitude($coords['lat']);
                $s->setLongitude($coords['lng']);
            }
        } elseif (isset($data['latitude'], $data['longitude'])) {
            $s->setLatitude($data['latitude']);
            $s->setLongitude($data['longitude']);
        }

        $this->em->persist($s);
        $this->em->flush();

        return $this->json([
            'message' => 'Signalement créé',
            'id'      => $s->getId()
        ]);
    }

    #[Route('/{id}/etat', name: 'ChangeEtat', methods: ['POST'])]
    public function changeEtat(Request $request, int $id): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkAnyRole($user, ['elu', 'administrateur'])) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $signalement = $this->em->getRepository(Signalements::class)->find($id);
        if (!$signalement) {
            return $this->json(["error" => "Signalement introuvable"], 404);
        }
        $etatSuivant = $signalement->getEtat()?->next();
        if (!$etatSuivant) {
            return $this->json(["error" => "Déjà résolu"], 400);
        }
        $signalement->setEtat($etatSuivant);

        $signalement->setDateModification(new \DateTime());
        $this->em->flush();

        return $this->json([
            "message"    => "État modifié",
            "nouvelEtat" => $nouvelEtat
        ]);
    }

    #[Route('/{id}', name: 'api_update_signalement', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }

        $signalement = $this->em->getRepository(Signalements::class)->find($id);
        if (!$signalement) {
            return $this->json(["error" => "Signalement introuvable"], 404);
        }

        // Seul le créateur ou un admin/elu peut modifier
        $estCreateur = $signalement->getUtilisateur()?->getId() === $user->getId();
        if (!$estCreateur && !$this->auth->checkAnyRole($user, ['elu', 'administrateur'])) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        // Modification uniquement si premier état
        if ($signalement->getEtat() !== EtatSignalement::ENREGISTRE) {
            return $this->json(["error" => "Impossible de modifier un signalement déjà pris en charge"], 403);
        }


        $data = json_decode($request->getContent(), true);

        $signalement->setTitre($data['titre'] ?? $signalement->getTitre());
        $signalement->setDescription($data['description'] ?? $signalement->getDescription());
        $signalement->setDateModification(new \DateTime());

        if (!empty($data['type'])) {
            $type = $this->em->getRepository(TypesSignalement::class)->findOneBy(['nom' => $data['type']]);
            if (!$type) {
                $type = new TypesSignalement();
                $type->setNom($data['type']);
                $this->em->persist($types);
            }
        }

        if (!empty($data['adresse'])) {
            $coords = $this->convertAdresse->addressToCoordinates($data['adresse']);
            if ($coords !== null) {
                $signalement->setLatitude($coords['lat']);
                $signalement->setLongitude($coords['lng']);
            }
        }

        $this->em->flush();

        return $this->json(['message' => 'Signalement modifié']);
    }

    #[Route('/{id}', name: 'api_delete_signalement', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }

        $signalement = $this->em->getRepository(Signalements::class)->find($id);
        if (!$signalement) {
            return $this->json(["error" => "Signalement introuvable"], 404);
        }

        // Seul le créateur ou un admin peut supprimer
        $estCreateur = $signalement->getUtilisateur()?->getId() === $user->getId();
        if (!$estCreateur && !$this->auth->checkAnyRole($user, ['elu', 'administrateur'])) {
            return $this->json(["error" => "Accès interdit"], 403);
        }

        // Suppression uniquement si premier état
        if ($signalement->getEtat() !== EtatSignalement::ENREGISTRE) {
            return $this->json(["error" => "Impossible de modifier un signalement déjà pris en charge"], 403);
        }

        $this->em->remove($signalement);
        $this->em->flush();

        return $this->json(['message' => 'Signalement supprimé']);
    }
}