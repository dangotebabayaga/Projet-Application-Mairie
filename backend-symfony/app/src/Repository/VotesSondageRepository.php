<?php

namespace App\Repository;

use App\Entity\VotesSondage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VotesSondage>
 */
class VotesSondageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VotesSondage::class);
    }

    //    /**
    //     * @return VotesSondage[] Returns an array of VotesSondage objects
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

    //    public function findOneBySomeField($value): ?VotesSondage
    //    {
    //        return $this->createQueryBuilder('alias')
    //            ->andWhere('alias.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
