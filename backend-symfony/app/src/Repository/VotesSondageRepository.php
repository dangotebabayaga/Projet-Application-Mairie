<?php
namespace App\Repository;

use App\Entity\VotesSondage;
use App\Entity\ListeChoixVote;
use App\Entity\Choix;
use App\Entity\Utilisateur;
use App\Entity\Sondages;
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
        $this->em = $em;
    }

    public function resultatSondage(int $idSondage): array
    {
        $conn = $this->em->getConnection();
        $sql = "
            SELECT c.nom AS choix, COUNT(lcv.id_vote) AS nb_votes
            FROM liste_choix_vote lcv
            JOIN votes_sondage vs ON lcv.id_vote = vs.id
            JOIN choix c ON lcv.id_choix = c.id
            WHERE vs.id_sondage = :idSondage
            GROUP BY c.nom
            ORDER BY nb_votes DESC
        ";  // correction : liens Markdown [lcv.id](http://...) → noms de colonnes SQL bruts

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('idSondage', $idSondage);
        return $stmt->executeQuery()->fetchAllAssociative();
    }

    /**
     * Enregistre un vote pour un Utilisateuret un choix
     * Ne crée rien si le vote existe déjà
     *
     * @param int $idUtilisateur
     * @param Choix $choix
     * @param int $idSondage
     */
    public function voteChoix(int $idUtilisateur, Choix $choix, int $idSondage): void
    {
        // Récupère les entités nécessaires
        $utilisateur= $this->em->getRepository(Utilisateur::class)->find($idUtilisateur);
        $sondage     = $this->em->getRepository(Sondages::class)->find($idSondage);

        if (!$utilisateur|| !$sondage) {
            throw new \Exception("Utilisateur ou sondage introuvable.");
        }

        // 1. Vérifie si l'Utilisateura déjà voté pour ce sondage
        $existingVote = $this->findOneBy([
            'utilisateur' => $utilisateur, // correction : citoyen → utilisateur
            'sondage'     => $sondage,
        ]);

        if (!$existingVote) {
            // 2. Crée le vote
            $vote = new VotesSondage();
            $vote->setUtilisateur($utilisateur); // correction : setCitoyen → setUtilisateur
            $vote->setSondage($sondage);
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

    public function voteChoixMultiple(int $idUtilisateur, array $choixList, int $idSondage): void
    {
        $utilisateur= $this->em->getRepository(Utilisateur::class)->find($idUtilisateur);
        $sondage     = $this->em->getRepository(Sondages::class)->find($idSondage);

        if (!$utilisateur|| !$sondage) {
            throw new \Exception("Utilisateur ou sondage introuvable.");
        }

        // 1. Vérifie si l'Utilisateura déjà voté pour ce sondage
        $existingVote = $this->findOneBy([
            'utilisateur' => $utilisateur, // correction : id bruts → objets Doctrine
            'sondage'     => $sondage,
        ]);

        if (!$existingVote) {
            // 2. Crée le vote
            $vote = new VotesSondage();
            $vote->setUtilisateur($utilisateur); // correction : setCitoyen → setUtilisateur
            $vote->setSondage($sondage);
            $vote->setDateVote(new \DateTime());
            $this->em->persist($vote);

            // 3. Crée la liaison vote <-> choix pour chaque choix
            foreach ($choixList as $choix) {
                $listeChoixVote = new ListeChoixVote();
                $listeChoixVote->setVote($vote);
                $listeChoixVote->setChoix($choix);
                $this->em->persist($listeChoixVote);
            }
        }
        // 4. Pas de flush ici : le contrôleur fera le flush global
    }
}