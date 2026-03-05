<?php
namespace App\Repository;

use App\Entity\Choix;
use App\Entity\Sondages;
use App\Entity\ListeChoixSondage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Choix>
 */
class ChoixRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Choix::class);
        $this->em = $em; // on stocke l'EntityManager injecté
    }

        /**
     * Crée ou lie les choix depuis une liste de noms pour un sondage donné
     *
     * @param Sondages $sondage
     * @param string[] $noms : liste de noms de choix
     */
    public function createOrLinkChoices(Sondages $sondage, array $noms): void
    {
        foreach ($noms as $nom) {
            // Cherche si le choix existe déjà
            $choix = $this->findOneBy(['nom' => $nom]);

            // Crée le choix si nécessaire
            if (!$choix) {
                $choix = new Choix();
                $choix->setNom($nom);
                $this->em->persist($choix);
            }

            // Vérifie si la liaison sondage <-> choix existe
            $exists = $this->em->getRepository(ListeChoixSondage::class)
                ->findOneBy(['sondage' => $sondage, 'choix' => $choix]);

            // Si pas de lien, crée la liaison
            if (!$exists) {
                $listeChoix = new ListeChoixSondage();
                $listeChoix->setSondage($sondage);
                $listeChoix->setChoix($choix);
                $this->em->persist($listeChoix);
            }
        }
    }
}