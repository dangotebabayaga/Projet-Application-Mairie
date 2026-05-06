<?php
namespace App\Repository;

use App\Entity\Signalements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Signalements>
 */
class SignalementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Signalements::class);
    }

    // Ajoutez vos méthodes personnalisées ici
}

