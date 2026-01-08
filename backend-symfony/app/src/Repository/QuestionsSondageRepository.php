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

    // Ajoutez vos méthodes personnalisées ici
}

