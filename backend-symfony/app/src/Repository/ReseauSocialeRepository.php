<?php
namespace App\Repository;

use App\Entity\ReseauSociale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReseauSociale>
 */
class ReseauSocialeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReseauSociale::class);
    }

    // Ajoutez vos méthodes personnalisées ici
}

