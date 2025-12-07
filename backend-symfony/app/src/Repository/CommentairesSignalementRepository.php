<?php

namespace App\Repository;

use App\Entity\CommentairesSignalement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentairesSignalement>
 */
class CommentairesSignalementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentairesSignalement::class);
    }

    //    /**
    //     * @return CommentairesSignalement[] Returns an array of CommentairesSignalement objects
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

    //    public function findOneBySomeField($value): ?CommentairesSignalement
    //    {
    //        return $this->createQueryBuilder('alias')
    //            ->andWhere('alias.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
