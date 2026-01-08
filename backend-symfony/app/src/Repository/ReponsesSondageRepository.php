<?php
namespace App\Repository;

use App\Entity\ReponsesSondage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReponsesSondage>
 */
class ReponsesSondageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReponsesSondage::class);
    }

    // Ajoutez vos méthodes personnalisées ici
}

