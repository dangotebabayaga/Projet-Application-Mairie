<?php
namespace App\Repository;

use App\Entity\TypeEv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeEv>
 */
class TypeEvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEv::class);
    }

    // Ajoutez vos méthodes personnalisées ici
}

