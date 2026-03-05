<?php
 namespace App\Controller;

use App\Entity\Sondages;
use App\Repository\ChoixRepository;
use App\Repository\SondagesRepository;
use App\Repository\VotesSondageRepository;
use App\Repository\AdministrateursRepository;
 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Annotation\Route;
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
    public function __construct(
        EntityManagerInterface $em,
        SondagesRepository $sondageRepo,
        ChoixRepository $choixRepo,
        VotesSondageRepository $votesRepo,
        AdministrateursRepository $adminRepo
        ) { 
            $this->em = $em;
            $this->sondageRepo=$sondageRepo;
            $this->choixRepo=$choixRepo; 
            $this->votesRepo=$votesRepo;
            $this->adminRepo=$adminRepo;
    } 

    #[Route('', name: 'api_get_sondages', methods: ['GET'])] 
    public function getAll(): JsonResponse { 
        $sondages = $this->em->getRepository(Sondages::class)->findAll(); 
        $data = array_map(fn($s) => [ 
            'id' => $s->getId(), 
            'titre' => $s->getTitre(), 
            'description' => $s->getDescription(),
            'dateDebut' => $s->getDateDebut(),
            'dateFin' => $s->getDateFin(), 
            'idAdmin' => $s->getAdministrateur()
        ], $sondages); 
        return $this->json($data);
    } 

   #[Route('', name: 'api_post_sondage', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // Vérifie que l'utilisateur est admin dans la table Administrateurs
        //if (!$this->adminRepo->isAdmin($data['administrateur_Id'])) {
        //    return $this->json(['error' => 'Accès interdit : vous n’êtes pas administrateur'], 403);
        //}

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

    #[Route('/api/sondages/{id}/resultat', name: 'sondage_resultat', methods: ['GET'])]
    public function resultat(int $id, VotesSondageRepository $votesRepo): JsonResponse
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
        $data = json_decode($request->getContent(), true);

        if (empty($data['idCitoyen']) || empty($data['choixId']) || empty($data['sondageId'])) {
            return $this->json(['error' => 'Données manquantes'], 400);
        }

        $choix = $this->choixRepo->find($data['choixId']);
        if (!$choix) {
            return $this->json(['error' => 'Choix invalide'], 400);
        }

        // Appel de la fonction voteChoix dans le repository
        $this->votesRepo->voteChoix(
            (int)$data['idCitoyen'],
            $choix,
            (int)$data['sondageId']
        );

        $this->em->flush();

        return $this->json([
            'message' => 'Vote enregistré avec succès',
            'citoyen_id' => $data['idCitoyen'],
            'choix_id' => $choix->getId(),
            'sondage_id' => $data['sondageId']
        ]);
    }

 }