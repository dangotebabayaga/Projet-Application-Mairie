<?php
 namespace App\Controller;

use App\Entity\Sondages;
use App\Repository\ChoixRepository;
use App\Repository\SondagesRepository;
use App\Repository\VotesSondageRepository;
use App\Repository\AdministrateursRepository;
use App\Entity\ListeChoixSondage;
use App\Repository\CitoyensRepository;
use App\Service\AuthChecker;
 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Attribute\Route;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

 #[Route('/api/sondages')] 
 class SondageApi extends AbstractController {
    private EntityManagerInterface $em;
    private SondagesRepository $sondageRepo;
    private ChoixRepository $choixRepo;
    private VotesSondageRepository $votesRepo;
    private AdministrateursRepository $adminRepo;
    private CitoyensRepository $citoyenRepo;
    private AuthChecker $auth;
    public function __construct(
        EntityManagerInterface $em,
        SondagesRepository $sondageRepo,
        ChoixRepository $choixRepo,
        VotesSondageRepository $votesRepo,
        AdministrateursRepository $adminRepo,
        CitoyensRepository $citoyenRepo,
        AuthChecker $auth
        ) { 
            $this->em = $em;
            $this->sondageRepo=$sondageRepo;
            $this->choixRepo=$choixRepo;
            $this->votesRepo=$votesRepo;
            $this->adminRepo=$adminRepo;
            $this->citoyenRepo = $citoyenRepo;
            $this->auth=$auth;
        } 

    #[Route('', name: 'api_get_sondages', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }

        $userId = (int) ($user['userId'] ?? 0);
        $role = $user['role'] ?? 'citoyen';

        // Récupérer quartier_id et categorie_id du citoyen connecté
        // (admin/superadmin voient tout)
        $citQuartier = null;
        $citCategorie = null;
        if ($role === 'citoyen') {
            $cit = $this->em->getConnection()->fetchAssociative(
                'SELECT quartier_id, categorie_id FROM citoyens WHERE utilisateur_id = :id',
                ['id' => $userId]
            );
            if ($cit) {
                $citQuartier = $cit['quartier_id'] !== null ? (int) $cit['quartier_id'] : null;
                $citCategorie = $cit['categorie_id'] !== null ? (int) $cit['categorie_id'] : null;
            }
        }

        $sondages = $this->em->getRepository(Sondages::class)->findAll();
        $data = [];

        foreach ($sondages as $s) {
            $sondageId = $s->getId();

            // Quartiers et catégories ciblés (vide = ouvert à tous)
            $quartiersIds = $this->fetchTargetIds('sondages_quartiers', 'quartier_id', $sondageId);
            $categoriesIds = $this->fetchTargetIds('sondages_categories', 'categorie_id', $sondageId);

            // Filtrage côté citoyen
            if ($role === 'citoyen') {
                $matchQuartier = empty($quartiersIds) || ($citQuartier !== null && in_array($citQuartier, $quartiersIds, true));
                $matchCategorie = empty($categoriesIds) || ($citCategorie !== null && in_array($citCategorie, $categoriesIds, true));
                if (!$matchQuartier || !$matchCategorie) {
                    continue;
                }
            }

            $relations = $this->em
                ->getRepository(ListeChoixSondage::class)
                ->findBy(['sondage' => $s]);

            $choix = array_map(function ($relation) {
                $c = $relation->getChoix();
                return [
                    'id' => $c->getId(),
                    'nom' => $c->getNom()
                ];
            }, $relations);

            $hasVoted = $this->votesRepo->findOneBy([
                'citoyen' => $userId,
                'sondage' => $sondageId
            ]) !== null;

            $nbVotants = $this->votesRepo->count(['sondage' => $sondageId]);

            $data[] = [
                'id' => $sondageId,
                'titre' => $s->getTitre(),
                'description' => $s->getDescription(),
                'dateDebut' => $s->getDateDebut(),
                'dateFin' => $s->getDateFin(),
                'idAdmin' => $s->getAdministrateur(),
                'choix' => $choix,
                'hasVoted' => $hasVoted,
                'nbVotants' => $nbVotants,
                'quartiers' => $quartiersIds,
                'categories' => $categoriesIds,
                'multiChoice' => $s->isMultiChoice()
            ];
        }

        return $this->json($data);
    }

    private function fetchTargetIds(string $table, string $column, int $sondageId): array
    {
        $rows = $this->em->getConnection()->fetchAllAssociative(
            "SELECT $column FROM $table WHERE sondage_id = :id",
            ['id' => $sondageId]
        );
        return array_map(fn($r) => (int) $r[$column], $rows);
    }

    #[Route('', name: 'api_post_sondage', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
         $user = $this->auth->getuserfromrequest($request);
        if (!$user) {
            return $this->json(["error" => "token manquant ou invalide"], 401);
        }

        if (!$this->auth->checkrole($user, 'admin')) {
            return $this->json(["error" => "accès interdit"], 403);
        }
        $data = json_decode($request->getContent(), true);

        // Création du sondage via le repository
        $sondage = $this->sondageRepo->createSondageFromData($data);

        // Création ou liaison des choix via le ChoixRepository
        if (!empty($data['choix']) && is_array($data['choix'])) {
            $this->choixRepo->createOrLinkChoices($sondage, $data['choix']);
        }

        // Flush pour générer l'id du sondage avant les liaisons
        $this->em->flush();

        $sondageId = $sondage->getId();
        $conn = $this->em->getConnection();

        // Liaison aux quartiers ciblés (vide = ouvert à tous les quartiers)
        if (!empty($data['quartiers']) && is_array($data['quartiers'])) {
            foreach ($data['quartiers'] as $qId) {
                $conn->insert('sondages_quartiers', [
                    'sondage_id' => $sondageId,
                    'quartier_id' => (int) $qId
                ]);
            }
        }

        // Liaison aux catégories ciblées (vide = ouvert à toutes les catégories)
        if (!empty($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $cId) {
                $conn->insert('sondages_categories', [
                    'sondage_id' => $sondageId,
                    'categorie_id' => (int) $cId
                ]);
            }
        }

        return $this->json([
            'message' => 'Sondage créé avec succès',
            'id' => $sondageId
        ]);
    } 

    #[Route('/{id}/resultat', name: 'sondage_resultat', methods: ['GET'])]
    public function resultat(int $id, Request $request, VotesSondageRepository $votesRepo): JsonResponse
    {
        $user = $this->auth->getuserfromrequest($request);
        if (!$user) {
            return $this->json(["error" => "token manquant ou invalide"], 401);
        }
        $result = $votesRepo->resultatSondage($id);

        return $this->json([
            'sondage_id' => $id,
            'resultats' => $result
        ]);
    }

    #[Route('/vote', name: 'api_vote_sondage', methods: ['POST'])]
    public function vote(Request $request): JsonResponse
    {
         $user = $this->auth->getuserfromrequest($request);
        if (!$user) {
            return $this->json(["error" => "token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkrole($user, 'citoyen')) {
            return $this->json(["error" => "accès interdit"], 403);
        }

        $data = json_decode($request->getContent(), true);

        // Vérifie que les champs nécessaires sont présents
        if (empty($data['citoyenId']) || empty($data['sondageId']) || empty($data['choixIds'])) {
            return $this->json(['error' => 'Données manquantes'], 400);
        }
    
        $citoyenId = (int) $data['citoyenId'];
        $sondageId = (int) $data['sondageId'];
        $choixIds = $data['choixIds'];
    
        // Si un seul choix est envoyé sous forme d'entier, le transforme en tableau
        if (!is_array($choixIds)) {
            $choixIds = [$choixIds];
        }
    
        if (empty($choixIds)) {
            return $this->json(['error' => 'Liste de choix invalide'], 400);
        }

        // Validation choix unique
        $sondageEntity = $this->em->getRepository(Sondages::class)->find($sondageId);
        if ($sondageEntity && !$sondageEntity->isMultiChoice() && count($choixIds) > 1) {
            return $this->json(['error' => 'Ce sondage n\'accepte qu\'une seule réponse'], 400);
        }
    
        // Transforme les IDs en entités Choix
        $choixEntities = [];
        foreach ($choixIds as $id) {
            $choix = $this->choixRepo->find((int) $id);
            if (!$choix) {
                return $this->json(['error' => "Choix invalide: $id"], 400);
            }
            $choixEntities[] = $choix;
        }
    
        // Appel du repository pour enregistrer le vote (gère maintenant 1 ou plusieurs choix)
        $this->votesRepo->voteChoixMultiple($citoyenId, $choixEntities, $sondageId);
    
        // Flush global
        $this->em->flush();
    
        return $this->json([
            'message' => 'Vote enregistré avec succès',
            'citoyen_id' => $citoyenId,
            'choix_ids' => array_map(fn($c) => $c->getId(), $choixEntities),
            'sondage_id' => $sondageId
        ]);
    } 
    
 }