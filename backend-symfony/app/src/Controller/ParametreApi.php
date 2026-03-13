<?php
namespace App\Controller;
use App\Service\AuthChecker;
 use App\Entity\Ville;
use App\Repository\AdministrateursRepository;
use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Attribute\Route;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 #[Route('/api/paramettre')] 
 class ParametreApi extends AbstractController {
    private EntityManagerInterface $em;
    private AdministrateursRepository $adminRepo;
    private AuthChecker $auth;

    public function __construct(EntityManagerInterface $em,AdministrateursRepository $adminRepo,
    AuthChecker $auth)
    {
        $this->em = $em;
        $this->adminRepo = $adminRepo;
        $this->auth=$auth;
    }

    
    #[Route('/{id}/theme', name: 'get_theme', methods: ['GET'])]
    public function getTheme(Request $request,int $id): JsonResponse{
         $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(["error" => "Token manquant ou invalide"], 401);
        }
        $ville=$this->em->getRepository(Ville::class)->findOneBy(['id'=>$id]);
        return $this->json([
            'slogan' => $ville->getSlogan(),
            'logo' => $ville->getLogo(),
            'theme' => $ville->getThemecouleur()
        ]);
    }

    #[Route('', name: 'updateVille', methods: ['POST'])]
    public function updateVille(Request $request): JsonResponse{

         $user = $this->auth->getuserfromrequest($request);
        if (!$user) {
            return $this->json(["error" => "token manquant ou invalide"], 401);
        }

        if (!$this->auth->checkrole($user, 'admin')) {
            return $this->json(["error" => "accès interdit"], 403);
        }
        $data = json_decode($request->getContent(), true);

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

    #[Route('/info', name: 'get_info', methods: ['GET'])]
    public function getInfo(): JsonResponse{
        $ville=$this->em->getRepository(Ville::class)->findOneBy(['id'=>'1']);
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