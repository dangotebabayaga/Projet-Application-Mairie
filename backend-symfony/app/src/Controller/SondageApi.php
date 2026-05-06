<?php
namespace App\Controller;

use App\Entity\Sondages;
use App\Repository\ChoixRepository;
use App\Repository\SondagesRepository;
use App\Repository\VotesSondageRepository;
use App\Repository\UtilisateurRepository; // correction : Administrateurs/CitoyensRepository → UtilisateurRepository
use App\Entity\ListeChoixSondage;
use App\Service\AuthChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/sondages')]
class SondageApi extends AbstractController
{
    private EntityManagerInterface $em;
    private SondagesRepository $sondageRepo;
    private ChoixRepository $choixRepo;
    private VotesSondageRepository $votesRepo;
    private UtilisateurRepository $userRepo;
    private AuthChecker $auth;

    public function __construct(
        EntityManagerInterface $em,
        SondagesRepository $sondageRepo,
        ChoixRepository $choixRepo,
        VotesSondageRepository $votesRepo,
        UtilisateurRepository $userRepo,
        AuthChecker $auth
    ) {
        $this->em          = $em;
        $this->sondageRepo = $sondageRepo;
        $this->choixRepo   = $choixRepo;
        $this->votesRepo   = $votesRepo;
        $this->userRepo    = $userRepo;
        $this->auth        = $auth;
    }

   #[Route('', name: 'api_get_sondages', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request); // null si non connecté

        $sondages = $this->em->getRepository(Sondages::class)->findAll();
        $data     = [];

        foreach ($sondages as $s) {
            $relations = $this->em
                ->getRepository(ListeChoixSondage::class)
                ->findBy(['sondage' => $s]);

            $choix = array_map(function ($relation) {
                $c = $relation->getChoix();
                return [
                    'id'  => $c->getId(),
                    'nom' => $c->getNom()
                ];
            }, $relations);

            // hasVoted uniquement si l'utilisateur est connecté
            $hasVoted = $user ? $this->votesRepo->findOneBy([
                'utilisateur' => $user,
                'sondage'     => $s
            ]) !== null : false;

            $data[] = [
                'id'          => $s->getId(),
                'titre'       => $s->getTitre(),
                'description' => $s->getDescription(),
                'dateDebut'   => $s->getDateDebut()?->format('Y-m-d H:i:s'),
                'dateFin'     => $s->getDateFin()?->format('Y-m-d H:i:s'),
                'idAdmin'     => $s->getAdministrateur()?->getId(),
                'choix'       => $choix,
                'hasVoted'    => $hasVoted
            ];
        }

        return $this->json($data);
    } 

    #[Route('', name: 'api_post_sondage', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request); 
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'administrateur')) { 
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $data = json_decode($request->getContent(), true);

        $sondage = $this->sondageRepo->createSondageFromData($data);

        if (!empty($data['choix']) && is_array($data['choix'])) {
            $this->choixRepo->createOrLinkChoices($sondage, $data['choix']);
        }

        $this->em->flush();

        return $this->json([
            'message' => 'Sondage créé avec succès',
            'id'      => $sondage->getId()
        ]);
    }

    #[Route('/{id}/resultat', name: 'sondage_resultat', methods: ['GET'])]
    public function resultat(int $id, Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }

        $result = $this->votesRepo->resultatSondage($id); // correction : paramètre inutile retiré du constructeur

        return $this->json([
            'sondage_id' => $id,
            'resultats'  => $result
        ]);
    }

    #[Route('/vote', name: 'api_vote_sondage', methods: ['POST'])]
    public function vote(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request); // correction : getuserfromrequest → getUserFromRequest
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkRole($user, 'citoyen')) { // correction : checkrole → checkRole
            return $this->json(["error" => "Accès interdit"], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['sondageId']) || empty($data['choixIds'])) { // correction : citoyenId retiré, on utilise $user
            return $this->json(['error' => 'Données manquantes'], 400);
        }

        $sondageId = (int) $data['sondageId'];
        $choixIds  = $data['choixIds'];

        if (!is_array($choixIds)) {
            $choixIds = [$choixIds];
        }

        if (empty($choixIds)) {
            return $this->json(['error' => 'Liste de choix invalide'], 400);
        }

        $choixEntities = [];
        foreach ($choixIds as $id) {
            $choix = $this->choixRepo->find((int) $id);
            if (!$choix) {
                return $this->json(['error' => "Choix invalide: $id"], 400);
            }
            $choixEntities[] = $choix;
        }

        // correction : citoyenId brut → id de l'Utilisateurconnecté
        $this->votesRepo->voteChoixMultiple($user->getId(), $choixEntities, $sondageId);

        $this->em->flush();

        return $this->json([
            'message'    => 'Vote enregistré avec succès',
            'citoyen_id' => $user->getId(), // correction : $citoyenId → $user->getId()
            'choix_ids'  => array_map(fn($c) => $c->getId(), $choixEntities),
            'sondage_id' => $sondageId
        ]);
    }
}