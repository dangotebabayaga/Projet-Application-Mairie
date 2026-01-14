<?php
 namespace App\Controller;

 use App\Service\ConvertAdresse;
 use App\Entity\Signalements;
 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 #[Route('/api/signalements')] 
 class SignalementsApi extends AbstractController {
    private EntityManagerInterface $em;
    private ConvertAdresse $convertAdresse;

    public function __construct(EntityManagerInterface $em, ConvertAdresse $convertAdresse)
    {
        $this->em = $em;
        $this->convertAdresse = $convertAdresse;
    }

    #[Route('', name: 'api_get_signalement', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $sondages = $this->em->getRepository(Signalements::class)->findAll();
    
        $data = array_map(function($s) {
            $lat = $s->getLatitude() ?? null;
            $lng = $s->getLongitude() ?? null;
        
            // Adresse via OpenStreetMap
            $adresse = ($lat !== null && $lng !== null) 
                ? $this->convertAdresse->coordinatesToAddress($lat, $lng)
                : null;
        
            return [
                'id' => $s->getId(),
                'titre' => $s->getTitre(),
                'etat' => $s->getEtat(),
                'description' => $s->getDescription(),
                'adresse' => $adresse,
                'typeId' => $s->getTypeId(),
                'citoyenId' => $s->getCitoyenId(),
                'dateCrea' => $s->getDateCreation(),
                'dateModif' => $s->getDateModification()
            ];
        }, $sondages);
    
        return $this->json($data);
    }


    #[Route('', name: 'api_post_signalement', methods: ['POST'])] 
    public function create(Request $request): JsonResponse{
         $data = json_decode($request->getContent(), true);

         $dateCrea = isset($data['dateCrea']) ? new \DateTime($data['dateCrea']) : new \DateTime();
         $dateModif = isset($data['dateModif']) ? new \DateTime($data['dateModif']) : new \DateTime();

         $s = new Signalements();
         $s->setTitre($data['titre'] ?? 'Sans titre');
         $s->setEtat($data['etat'] ?? 'nouveau');
         $s->setDescription($data['description'] ?? null);
         $s->setDateCreation($dateCrea);
         $s->setDateModification($dateModif);

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
             $s->setLatitude($data['latitude']);
             $s->setLongitude($data['longitude']);
         }

         $this->em->persist($s);
         $this->em->flush();

         return $this->json([
             'message' => 'Signalement créé',
             'id' => $s->getId()
         ]);
    }
 }