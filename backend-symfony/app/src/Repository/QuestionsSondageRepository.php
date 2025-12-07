<?php

namespace App\Repository;

use App\Entity\QuestionsSondage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionsSondage>
 */
class QuestionsSondageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionsSondage::class);
    }

    //    /**
    //     * @return QuestionsSondage[] Returns an array of QuestionsSondage objects
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

    //    public function findOneBySomeField($value): ?QuestionsSondage
    //    {
    //        return $this->createQueryBuilder('alias')
    //            ->andWhere('alias.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
