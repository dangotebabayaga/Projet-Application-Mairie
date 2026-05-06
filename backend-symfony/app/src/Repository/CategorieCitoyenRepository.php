<?php
namespace App\Repository;

use App\Entity\CategorieCitoyen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategorieCitoyenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieCitoyen::class);
    }

    public function findByVille(int $villeId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.villeId = :v')
            ->setParameter('v', $villeId)
            ->orderBy('c.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
