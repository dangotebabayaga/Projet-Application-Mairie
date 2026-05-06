<?php
namespace App\Repository;

use App\Entity\Evenement;
use App\Entity\TypeEv;
use App\Entity\Utilisateur;
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
        $this->em = $em; // correction : assignation manquante
    }

    public function crea(array $data): Evenement
    {
        // Dates et heures
        $dateEv   = isset($data['date Evenement']) ? new \DateTime($data['date Evenement']) : new \DateTime();
        $heureDeb = isset($data['Heure début'])    ? new \DateTime($data['Heure début'])    : new \DateTime();
        $heureFin = isset($data['Heure fin'])       ? new \DateTime($data['Heure fin'])       : new \DateTime();

        // Gestion du type
        $type = null;
        if (!empty($data['type'])) {
            $type = $this->em->getRepository(TypeEv::class)->findOneBy(['nom' => $data['type']]);
            if (!$type) {
                $type = new TypeEv();
                $type->setNom($data['type']);
                $this->em->persist($type);
                $this->em->flush();
            }
        }

        // Récupération de l'administrateur (objet Utilisateur)
        $administrateur = null;
        if (!empty($data['administrateurId'])) {
            $administrateur = $this->em->getRepository(Utilisateur::class)->find($data['administrateurId']);
        }

        // Création de l'événement
        $evenement = new Evenement();
        $evenement->setTitre($data['titre'] ?? null);
        $evenement->setLieux($data['lieux'] ?? null);
        $evenement->setCommentaire($data['commentaire'] ?? null);
        $evenement->setDateEv($dateEv);
        $evenement->setHeureDeb($heureDeb);
        $evenement->setHeureFin($heureFin);
        $evenement->setadministrateur($administrateur);  // correction : objet Utilisateur, pas un id brut
        $evenement->setType($type);             // correction : objet TypeEv, pas un id brut

        $this->em->persist($evenement);
        return $evenement;
    }
}