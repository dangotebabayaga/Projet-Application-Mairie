<?php
namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Administrateurs>
 */
class AdministrateursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    /**
     * Vérifie si un utilisateur est administrateur
     *
     * @param int $userId
     * @return bool
     */
    public function isAdmin(int $userId): bool
    {
        $admin = $this->findOneBy(['utilisateur_id' => $userId]);
        return $admin !== null;
    }

}

