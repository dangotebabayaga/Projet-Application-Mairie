<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Entity\Quartier;
use App\Entity\Utilisateur;
use App\Entity\Signalement;
use App\Entity\Sondage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test/relations', name: 'test_relations')]
    public function testRelations(EntityManagerInterface $entityManager): JsonResponse
    {
        $results = [];

        // TEST 1: Récupérer une ville avec ses relations (collections)
        $ville = $entityManager->getRepository(Ville::class)->findOneBy([]);
        if ($ville) {
            $results['test_1_ville'] = [
                'id' => $ville->getId(),
                'nom' => $ville->getNomVille(),
                'nombre_quartiers' => $ville->getQuartiers()->count(),
                'nombre_utilisateurs' => $ville->getUtilisateurs()->count(),
                'nombre_sondages' => $ville->getSondages()->count(),
                'nombre_evenements' => $ville->getEvenements()->count(),
            ];
        }

        // TEST 2: Récupérer un quartier avec sa relation Ville
        $quartier = $entityManager->getRepository(Quartier::class)->findOneBy([]);
        if ($quartier) {
            $results['test_2_quartier'] = [
                'id' => $quartier->getId(),
                'ville_id' => $quartier->getVille() ? $quartier->getVille()->getId() : null,
                'ville_existe' => $quartier->getVille() !== null,
            ];
        }

        // TEST 3: Récupérer un utilisateur avec ses 3 relations
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->findOneBy([]);
        if ($utilisateur) {
            $results['test_3_utilisateur'] = [
                'id' => $utilisateur->getId(),
                'nom_complet' => $utilisateur->getNom() . ' ' . $utilisateur->getPrenom(),
                'ville_id' => $utilisateur->getVille() ? $utilisateur->getVille()->getId() : null,
                'quartier_id' => $utilisateur->getQuartier() ? $utilisateur->getQuartier()->getId() : null,
                'categorie_id' => $utilisateur->getCategorie() ? $utilisateur->getCategorie()->getId() : null,
            ];
        }

        // TEST 4: Récupérer un signalement avec toutes ses relations
        $signalement = $entityManager->getRepository(Signalement::class)->findOneBy([]);
        if ($signalement) {
            $results['test_4_signalement'] = [
                'id' => $signalement->getId(),
                'titre' => $signalement->getTitre(),
                'citoyen_id' => $signalement->getCitoyen() ? $signalement->getCitoyen()->getId() : null,
                'quartier_id' => $signalement->getQuartier() ? $signalement->getQuartier()->getId() : null,
                'fichier_existe' => $signalement->getFichier() !== null,
            ];
        }

        // TEST 5: Récupérer un sondage avec ses relations
        $sondage = $entityManager->getRepository(Sondage::class)->findOneBy([]);
        if ($sondage) {
            $results['test_5_sondage'] = [
                'id' => $sondage->getId(),
                'titre' => $sondage->getTitre(),
                'ville_id' => $sondage->getVille() ? $sondage->getVille()->getId() : null,
                'createur_id' => $sondage->getCreePar() ? $sondage->getCreePar()->getId() : null,
            ];
        }

        // TEST 6: Compter les entités
        $results['test_6_statistiques'] = [
            'total_villes' => $entityManager->getRepository(Ville::class)->count([]),
            'total_quartiers' => $entityManager->getRepository(Quartier::class)->count([]),
            'total_utilisateurs' => $entityManager->getRepository(Utilisateur::class)->count([]),
            'total_signalements' => $entityManager->getRepository(Signalement::class)->count([]),
            'total_sondages' => $entityManager->getRepository(Sondage::class)->count([]),
        ];

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Tous les tests de relations ont été exécutés',
            'results' => $results,
        ], 200);
    }

    #[Route('/test/query', name: 'test_query')]
    public function testAdvancedQuery(EntityManagerInterface $entityManager): JsonResponse
    {
        // Test d'une requête avec jointures
        $qb = $entityManager->createQueryBuilder();

        $signalements = $qb->select('s', 'c', 'q', 'v')
            ->from(Signalement::class, 's')
            ->leftJoin('s.citoyen', 'c')
            ->leftJoin('s.quartier', 'q')
            ->leftJoin('q.ville', 'v')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($signalements as $signalement) {
            $results[] = [
                'titre' => $signalement->getTitre(),
                'etat' => $signalement->getEtat(),
                'citoyen' => $signalement->getCitoyen() ? [
                    'nom' => $signalement->getCitoyen()->getNom(),
                    'prenom' => $signalement->getCitoyen()->getPrenom(),
                ] : null,
                'quartier' => $signalement->getQuartier() ? [
                    'nom' => $signalement->getQuartier()->getNomQuartier(),
                    'ville' => $signalement->getQuartier()->getVille()->getNomVille(),
                ] : null,
            ];
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Requête avancée avec jointures exécutée',
            'nombre_signalements' => count($results),
            'signalements' => $results,
        ], 200);
    }

    #[Route('/test/db', name: 'test_db')]
    public function testDatabaseConnection(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Test simple : compter les villes
            $connection = $entityManager->getConnection();
            $sql = 'SELECT COUNT(*) as total FROM villes';
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();
            $count = $result->fetchOne();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Connexion à la base de données OK',
                'total_villes' => $count,
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
