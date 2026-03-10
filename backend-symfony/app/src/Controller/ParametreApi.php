<?php
namespace App\Controller;

 use App\Entity\Ville;
use App\Repository\AdministrateursRepository;
use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 #[Route('/api/paramettre')] 
 class ParametreApi extends AbstractController {
    private EntityManagerInterface $em;
    private AdministrateursRepository $adminRepo;

    public function __construct(EntityManagerInterface $em,AdministrateursRepository $adminRepo)
    {
        $this->em = $em;
        $this->adminRepo = $adminRepo;
    }

    
    #[Route('/{id}/theme', name: 'get_theme', methods: ['GET'])]
    public function getTheme(int $id): JsonResponse{
        $ville=$this->em->getRepository(Ville::class)->findOneBy(['id'=>$id]);
        return $this->json([
            'slogan' => $ville->getSlogan(),
            'logo' => $ville->getLogo(),
            'theme' => $ville->getThemecouleur()
        ]);
    }

    #[Route('', name: 'updateVille', methods: ['POST'])]
    public function updateVille(Request $request): JsonResponse{

        $data = json_decode($request->getContent(), true);

        if (!$this->adminRepo->isAdmin($data['administrateur_Id'])) {
            return $this->json(['error' => "Accès interdit : vous n'êtes pas administrateur"], 403);
        }

        $ville=$this->em->getRepository(Ville::class)->findOneBy(['id'=>$data['id']]);

        $ville->setLogo($data['logo']);
        $ville->setNom($data['nom']);
        $ville->setSlogan($data['slogan']);
        $ville->setThemecouleur($data['theme']);

        $this->em->flush();

        return $this->json([
            'message'=>"La ville à était modifié"
        ]);
    }

    #[Route('/{id}/info', name: 'get_info', methods: ['GET'])]
    public function getInfo(int $id): JsonResponse{
        $ville=$this->em->getRepository(Ville::class)->findOneBy(['id'=>$id]);
        return $this->json([
            'id' => $ville->getId(),
            'nom' => $ville->getNom(),
            'slogan' => $ville->getSlogan(),
            'logo' => $ville->getLogo(),
            'theme' => $ville->getThemecouleur(),
            'dateCrea' => $ville->getDateCreation()
        ]);
    }



 }