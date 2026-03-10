<?php
namespace App\Repository;

use App\Entity\Citoyens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Citoyens>
 */
class CitoyensRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Citoyens::class);
    }

    /**
     * Vérifie si un utilisateur est administrateur
     *
     * @param int $userId
     * @return bool
     */
    public function isCitoyen(int $userId): bool
    {
        $cit = $this->findOneBy(['utilisateurId' => $userId]);
        return $cit !== null;
    }
}

