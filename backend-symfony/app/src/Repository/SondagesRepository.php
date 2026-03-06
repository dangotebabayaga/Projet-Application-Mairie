<?php
namespace App\Repository;

use App\Entity\Sondages;
use App\Entity\Admin;
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
        $this->em = $em; // on stocke l'EntityManager injecté
    }


    /**
     * Crée un sondage depuis les données fournies
     *
     * @param array $data : ['titre' => ..., 'description' => ..., 'dateDebut' => ..., 'dateFin' => ...]
     * @param Administrateurs $admin : l'administrateur qui crée le sondage
     * @return Sondages : entité sondage créée
     */
    public function createSondageFromData(array $data): Sondages
    {
        $dateDebut = new \DateTime($data['dateDebut']);
        $dateFin = new \DateTime($data['dateFin']);

        $sondage = new Sondages();
        $sondage->setTitre($data['titre'] ?? 'Sans titre');
        $sondage->setDescription($data['description'] ?? null);
        $sondage->setDateDebut($dateDebut);
        $sondage->setDateFin($dateFin);
        $sondage->setAdminstrateur($data['administrateur_Id']);

        // On ne flush pas ici, le contrôleur s'en charge
        $this->em->persist($sondage);

        return $sondage;
    }
}

