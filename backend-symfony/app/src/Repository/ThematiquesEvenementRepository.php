<?php

namespace App\Repository;

use App\Entity\ThematiquesEvenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ThematiquesEvenement>
 */
class ThematiquesEvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThematiquesEvenement::class);
    }

    //    /**
    //     * @return ThematiquesEvenement[] Returns an array of ThematiquesEvenement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('alias')
    //            ->andWhere('alias.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('alias.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ThematiquesEvenement
    //    {
    //        return $this->createQueryBuilder('alias')
    //            ->andWhere('alias.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
