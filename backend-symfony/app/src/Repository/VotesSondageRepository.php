<?php
namespace App\Repository;

use App\Entity\VotesSondage;
use App\Entity\ListeChoixVote;
use App\Entity\Choix;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<VotesSondage>
 */
class VotesSondageRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, VotesSondage::class);
        $this->em = $em; // on stocke l'EntityManager injecté
    }

    public function resultatSondage(int $idSondage)
    {
        $conn = $this->em->getConnection();
    
        $sql = "
            SELECT c.nom AS choix, COUNT(lcv.id_vote) AS nb_votes
            FROM liste_choix_vote lcv
            JOIN votes_sondage vs ON lcv.id_vote = vs.id
            JOIN choix c ON lcv.id_choix = c.id
            WHERE vs.question_id = :idSondage
            GROUP BY c.nom
            ORDER BY nb_votes DESC
        ";
    
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('idSondage', $idSondage);
        $result = $stmt->executeQuery()->fetchAllAssociative();
    
        return $result;
    }

     /**
     * Enregistre un vote pour un citoyen et un choix
     * Ne crée rien si le vote existe déjà
     *
     * @param int $idCitoyen
     * @param Choix $choix
     * @param int $idSondage
     */
    public function voteChoix(int $idCitoyen, Choix $choix, int $idSondage): void
    {
        // 1. Vérifie si le citoyen a déjà voté pour ce sondage
        $existingVote = $this->findOneBy([
            'citoyenId' => $idCitoyen,
            'questionId' => $idSondage
        ]);

        if (!$existingVote) {
            // 2. Crée le vote
            $vote = new VotesSondage();
            $vote->setCitoyenId($idCitoyen);
            $vote->setSondageId($idSondage);
            $vote->setDateVote(new \DateTime());
            $this->em->persist($vote);

            // 3. Crée la liaison vote <-> choix
            $listeChoixVote = new ListeChoixVote();
            $listeChoixVote->setVote($vote);
            $listeChoixVote->setChoix($choix);
            $this->em->persist($listeChoixVote);
        }
        // 4. Pas de flush ici : le contrôleur fait le flush global
    }


}

