#!/usr/bin/env php
<?php
/**
 * Script pour créer tous les Repository manquants
 */

$entities = [
    'Actualite',
    'CategoriesCitoyen',
    'CommentairesSignalement',
    'Evenement',
    'EvenementsUtilisateur',
    'Fichier',
    'HistoriqueSignalement',
    'LogAction',
    'Notification',
    'Quartier',
    'QuestionsSondage',
    'ReponsesSondage',
    'ReseauSocial',
    'Signalement',
    'Sondage',
    'ThematiquesEvenement',
    'Utilisateur',
    'Ville',
    'VotesSondage',
];

$repositoryDir = __DIR__ . '/src/Repository';

echo "Creation des Repositories...\n\n";

foreach ($entities as $entity) {
    $repositoryName = $entity . 'Repository';
    $repositoryFile = $repositoryDir . '/' . $repositoryName . '.php';

    if (file_exists($repositoryFile)) {
        echo "  SKIP $repositoryName (existe deja)\n";
        continue;
    }

    $content = "<?php

namespace App\Repository;

use App\Entity\\$entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<$entity>
 */
class $repositoryName extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry \$registry)
    {
        parent::__construct(\$registry, {$entity}::class);
    }

    //    /**
    //     * @return {$entity}[] Returns an array of {$entity} objects
    //     */
    //    public function findByExampleField(\$value): array
    //    {
    //        return \$this->createQueryBuilder('alias')
    //            ->andWhere('alias.exampleField = :val')
    //            ->setParameter('val', \$value)
    //            ->orderBy('alias.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField(\$value): ?{$entity}
    //    {
    //        return \$this->createQueryBuilder('alias')
    //            ->andWhere('alias.exampleField = :val')
    //            ->setParameter('val', \$value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
";

    file_put_contents($repositoryFile, $content);
    echo "  OK $repositoryName.php cree\n";
}

echo "\nTermine ! Tous les Repositories ont ete crees.\n";
