<?php
namespace App\Controller;

use App\Service\AuthChecker;
use App\Entity\Evenement;
use App\Entity\TypeEv;
use App\Repository\EvenementRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/evenement')]
class EvenementApi extends AbstractController
{
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/evenement')]
class EvenementApi extends AbstractController
{
    private EntityManagerInterface $em;
    private EvenementRepository $evenRepo;
    private UtilisateurRepository $userRepo; // correction : administrateursRepository → UtilisateurRepository
    private UtilisateurRepository $userRepo; // correction : administrateursRepository → UtilisateurRepository
    private AuthChecker $auth;

    public function __construct(
        EntityManagerInterface $em,
        EvenementRepository $evenRepo,
        UtilisateurRepository $userRepo, // correction : administrateursRepository → UtilisateurRepository
        AuthChecker $auth
    ) {
        $this->em       = $em;
        $this->evenRepo = $evenRepo;
        $this->userRepo = $userRepo;
        $this->auth     = $auth;
    public function __construct(
        EntityManagerInterface $em,
        EvenementRepository $evenRepo,
        UtilisateurRepository $userRepo, // correction : administrateursRepository → UtilisateurRepository
        AuthChecker $auth
    ) {
        $this->em       = $em;
        $this->evenRepo = $evenRepo;
        $this->userRepo = $userRepo;
        $this->auth     = $auth;
    }

    #[Route('', name: 'create_Even', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
    #[Route('', name: 'create_Even', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        if (!$this->auth->checkAnyRole($user, ['elu', 'administrateur'])) {
        if (!$this->auth->checkAnyRole($user, ['elu', 'administrateur'])) {
            return $this->json(["error" => "Accès interdit"], 403);
        }


        $data = json_decode($request->getContent(), true);
        $ev   = $this->evenRepo->crea($data);
        $this->em->flush(); // correction : persist déjà fait dans crea(), flush suffit
        $ev   = $this->evenRepo->crea($data);
        $this->em->flush(); // correction : persist déjà fait dans crea(), flush suffit

        return $this->json([
            'message' => 'Événement créé',
            'id'      => $ev->getId()
        ]);
        return $this->json([
            'message' => 'Événement créé',
            'id'      => $ev->getId()
        ]);
    }


    #[Route('', name: 'get_all_Even', methods: ['GET'])]
    public function getall(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
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
                "titre"          => $e->getTitre(),
                "lieux"          => $e->getLieux(),
                "commentaire"    => $e->getCommentaire(),
                "titre"          => $e->getTitre(),
                "lieux"          => $e->getLieux(),
                "commentaire"    => $e->getCommentaire(),
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

    #[Route('/listeType', name: 'get_all_TypeEv', methods: ['GET'])] // correction : nom de route dupliqué

    #[Route('/listeType', name: 'get_all_TypeEv', methods: ['GET'])] // correction : nom de route dupliqué
    public function getallType(Request $request): JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        $type = $this->em->getRepository(TypeEv::class)->findAll();
        $data = [];

        foreach ($type as $e) {

        $types = $this->em->getRepository(TypeEv::class)->findAll();
        $data  = [];
        foreach ($types as $t) {
        $types = $this->em->getRepository(TypeEv::class)->findAll();
        $data  = [];
        foreach ($types as $t) {
            $data[] = [
                "type" => $t->getNom()
                "type" => $t->getNom()
            ];
        }

        return $this->json($data);
    }
}
}