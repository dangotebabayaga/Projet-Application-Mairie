<?php
namespace App\Repository;

use App\Entity\VotesSondage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VotesSondage>
 */
class VotesSondageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VotesSondage::class);
    }

    // Ajoutez vos méthodes personnalisées ici
}

