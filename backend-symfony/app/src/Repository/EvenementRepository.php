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

    /**
     * Met à jour un événement existant. Champs absents = inchangés.
     */
    public function maj(Evenement $ev, array $data): void
    {
        $em = $this->getEntityManager();

        if (array_key_exists('titre', $data)) $ev->setTitre($data['titre'] ?: null);
        if (array_key_exists('lieux', $data)) $ev->setLieux($data['lieux'] ?: null);
        if (array_key_exists('commentaire', $data)) $ev->setCommentaire($data['commentaire'] ?: null);
        if (!empty($data['date Evenement'])) $ev->setDateEv(new \DateTime($data['date Evenement']));
        if (!empty($data['Heure début'])) $ev->setHeureDeb(new \DateTime($data['Heure début']));
        if (!empty($data['Heure fin'])) $ev->setHeureFin(new \DateTime($data['Heure fin']));

        if (array_key_exists('type', $data) && $data['type']) {
            $type = $em->getRepository(TypeEv::class)->findOneBy(['nom' => $data['type']]);
            if (!$type) {
                $type = new TypeEv();
                $type->setNom($data['type']);
                $em->persist($type);
                $em->flush();
            }
            $ev->setType($type->getId());
        }
    }
}

