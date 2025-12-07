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

    //    /**
    //     * @return ReponsesSondage[] Returns an array of ReponsesSondage objects
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

    //    public function findOneBySomeField($value): ?ReponsesSondage
    //    {
    //        return $this->createQueryBuilder('alias')
    //            ->andWhere('alias.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
