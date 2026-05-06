<?php
namespace App\Repository;

use App\Entity\Quartier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuartierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quartier::class);
    }

    public function findByVille(int $villeId): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.villeId = :v')
            ->setParameter('v', $villeId)
            ->orderBy('q.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
