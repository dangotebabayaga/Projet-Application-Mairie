<?php
namespace App\Repository;

use App\Entity\Citoyens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Citoyens>
 */
class CitoyensRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Citoyens::class);
    }

    // Ajoutez vos méthodes personnalisées ici
}

