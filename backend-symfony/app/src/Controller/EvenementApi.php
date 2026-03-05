<?php
namespace App\Controller;

 use App\Entity\Evenement;
 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 #[Route('/api/evenement')] 
 class EvenementApi extends AbstractController {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

 }