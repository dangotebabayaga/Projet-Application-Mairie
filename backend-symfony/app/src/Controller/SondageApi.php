<?php
 namespace App\Controller;
 use App\Entity\Sondages;
 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 #[Route('/api/sondages')] 
 class SondageApi extends AbstractController {
    private EntityManagerInterface $em; 
    public function __construct(
        EntityManagerInterface $em) { $this->em = $em; 
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
    public function create(Request $request): JsonResponse { 
        $data = json_decode($request->getContent(), true); 
        $dateDebut = new \DateTime($data['dateDebut']);
        $dateFin = new \DateTime($data['dateFin']);
        $sondage = new Sondages(); 
        $sondage->setTitre($data['titre'] ?? 'Sans titre'); 
        $sondage->setDescription($data['description'] ?? null); 
        $sondage->setDateDebut($dateDebut);
        $sondage->setDateFin($dateFin);
        $sondage->setAdminstrateur($data['idAdmin']);
        $this->em->persist($sondage); 
        $this->em->flush(); 
        return $this->json([ 'message' => 'Sondage créé', 'id' => $sondage->getId() ]); 
    }
 }