<?php
namespace App\Repository;

use App\Entity\Evenement;
use App\Entity\TypeEv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Evenement>
 */
class EvenementRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function crea(array $data): Evenement
    {
        $em = $this->getEntityManager();

        // Dates et heures
        $dateEv  = isset($data['date Evenement']) ? new \DateTime($data['date Evenement']) : new \DateTime();
        $heureDeb = isset($data['Heure début']) ? new \DateTime($data['Heure début']) : new \DateTime();
        $heureFin = isset($data['Heure fin']) ? new \DateTime($data['Heure fin']) : new \DateTime();

        // Gestion du type
        $type = null;
        if (!empty($data['type'])) {
            $type = $em->getRepository(TypeEv::class)->findOneBy(['nom' => $data['type']]);
            if (!$type) {
                $type = new TypeEv();
                $type->setNom($data['type']);
                $em->persist($type);
                $em->flush(); // on flush pour récupérer l'id
            }
        }

        // Création de l'événement
        $s = new Evenement();
        $s->setTitre($data['titre'] ?? null);
        $s->setLieux($data['lieux'] ?? null);
        $s->setCommentaire($data['commentaire'] ?? null);
        $s->setDateEv($dateEv);
        $s->setHeureDeb($heureDeb);
        $s->setHeureFin($heureFin);
        $s->setAdministrateurId($data['adminId'] ?? null);
        $s->setType($type ? $type->getId() : null);

        // On persiste mais on ne flush pas encore si tu veux le faire plus tard
        $em->persist($s);

        return $s;
    }
}

