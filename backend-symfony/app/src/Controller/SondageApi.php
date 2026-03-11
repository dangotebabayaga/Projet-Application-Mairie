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

        $sondages = $this->em->getRepository(Sondages::class)->findAll();

        $data = [];

        foreach ($sondages as $s) {

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

            $data[] = [
                'id' => $s->getId(),
                'titre' => $s->getTitre(),
                'description' => $s->getDescription(),
                'dateDebut' => $s->getDateDebut(),
                'dateFin' => $s->getDateFin(),
                'idAdmin' => $s->getAdministrateur(),
                'choix' => $choix
            ];
        }

        return $this->json($data);
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

        // Flush unique pour enregistrer sondage et choix
        $this->em->flush();

        return $this->json([
            'message' => 'Sondage créé avec succès',
            'id' => $sondage->getId()
        ]);
    } 

    #[Route('/{id}/resultat', name: 'sondage_resultat', methods: ['GET'])]
    public function resultat(int $id, Request $request, VotesSondageRepository $votesRepo): JsonResponse
    {
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