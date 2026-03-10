<?php
namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\TypeEv;
use App\Repository\EvenementRepository;
 use App\Repository\AdministrateursRepository;
 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 #[Route('/api/evenement')] 
 class EvenementApi extends AbstractController {
    private EntityManagerInterface $em;
    private EvenementRepository $evenRepo;
    private AdministrateursRepository $adminRepo;

    public function __construct(EntityManagerInterface $em, EvenementRepository $evenRepo, AdministrateursRepository $adminRepo)
    {
        $this->em = $em;
        $this->evenRepo=$evenRepo;
        $this->adminRepo=$adminRepo;
    }

    #[Route('', name: 'create_Even', methods: ['POST'])] 
    public function create(Request $request): JsonResponse{
         $data = json_decode($request->getContent(), true);
         
         // Vérifie que l'utilisateur est admin dans la table Administrateurs
         if (!$this->adminRepo->isAdmin($data['adminId'])) {
             return $this->json(['error' => "Accès interdit : vous n'êtes pas administrateur"], 403);
         }

         $ev=$this->evenRepo->crea($data);

         $this->em->persist($ev);
         $this->em->flush();

         return $this->json([
             'message' => 'Signalement créé',
             'id' => $ev->getId()
         ]);
    }
    
    #[Route('', name: 'get_all_Even', methods: ['GET'])]
    public function getall(): JsonResponse
    {
        $even = $this->em->getRepository(Evenement::class)->findAll();

        $data = [];

        foreach ($even as $e) {

            $type = $this->em->getRepository(TypeEv::class)->find($e->getType());

            $data[] = [
                "titre" => $e->getTitre(),
                "lieux" => $e->getLieux(),
                "commentaire" => $e->getCommentaire(),
                "date Evenement" => $e->getDateEv()?->format('Y-m-d'),
                "Heure début" => $e->getHeureDeb()?->format('H:i'),
                "Heure fin" => $e->getHeureFin()?->format('H:i'),
                "adminId" => $e->getAdministrateurId(),
                "type" => $type?->getNom()
            ];
        }

        return $this->json($data);
    }
    #[Route('/listeType', name: 'get_all_Even', methods: ['GET'])]
    public function getallType(): JsonResponse
    {
        $type = $this->em->getRepository(TypeEv::class)->findAll();
        $data = [];

        foreach ($type as $e) {

            $data[] = [
                "type" => $e->getNom()
            ];
        }

        return $this->json($data);
    }

 }