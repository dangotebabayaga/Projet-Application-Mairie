<?php
namespace App\Repository;

use App\Entity\TypesSignalement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypesSignalement>
 */
class TypesSignalementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypesSignalement::class);
    }

    // Ajoutez vos méthodes personnalisées ici
}

