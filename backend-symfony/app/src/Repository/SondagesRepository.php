<?php
namespace App\Repository;

use App\Entity\Sondages;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Sondages>
 */
class SondagesRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Sondages::class);
        $this->em = $em;
    }

    /**
     * Crée un sondage depuis les données fournies
     *
     * @param array $data : ['titre' => ..., 'description' => ..., 'dateDebut' => ..., 'dateFin' => ..., 'administrateur_Id' => ...]
     * @return Sondages : entité sondage créée
     */
    public function createSondageFromData(array $data): Sondages
    {
        $dateDebut = new \DateTime($data['dateDebut']);
        $dateFin   = new \DateTime($data['dateFin']);

        // correction : administrateur → administrateur
        $administrateur = null;
        if (!empty($data['administrateur_Id'])) {
            $administrateur = $this->em->getRepository(Utilisateur::class)->find($data['administrateur_Id']);
        }

        $sondage = new Sondages();
        $sondage->setTitre($data['titre'] ?? 'Sans titre');
        $sondage->setDescription($data['description'] ?? null);
        $sondage->setDateDebut($dateDebut);
        $sondage->setDateFin($dateFin);
        $sondage->setAdministrateur($administrateur); // correction : setadministrateur → setAdministrateur

        $this->em->persist($sondage);
        return $sondage;
    }
}