#!/usr/bin/env php
<?php
/**
 * Script final pour nettoyer toutes les propriétés INT dupliquées
 */

$entitiesDir = __DIR__ . '/src/Entity';

echo "Nettoyage des entites...\n\n";

$files = [
    'Quartier.php',
    'CategoriesCitoyen.php',
    'ThematiquesEvenement.php',
    'Utilisateur.php',
    'Fichier.php',
    'Signalement.php',
    'HistoriqueSignalement.php',
    'CommentairesSignalement.php',
    'Sondage.php',
    'QuestionsSondage.php',
    'ReponsesSondage.php',
    'VotesSondage.php',
    'Evenement.php',
    'EvenementsUtilisateur.php',
    'Actualite.php',
    'ReseauSocial.php',
    'Notification.php',
    'LogAction.php',
];

foreach ($files as $filename) {
    $filepath = $entitiesDir . '/' . $filename;

    if (!file_exists($filepath)) {
        echo "  SKIP $filename\n";
        continue;
    }

    echo "  Processing $filename...\n";

    $content = file_get_contents($filepath);
    $originalContent = $content;

    // Supprimer toutes les lignes de type:
    // #[ORM\Column(name: 'ville_id', type: 'integer')]
    // private ?int $villeId = null;
    $pattern = "/#\[ORM\\\\Column\(name: '[^']+_id', type: 'integer'\)\]\s*\n\s*private \?int \$\w+ = null;\s*\n/";
    $content = preg_replace($pattern, '', $content);

    // Variante avec _par
    $pattern2 = "/#\[ORM\\\\Column\(name: '[^']+_par', type: 'integer'\)\]\s*\n\s*private \?int \$\w+ = null;\s*\n/";
    $content = preg_replace($pattern2, '', $content);

    if ($content !== $originalContent) {
        file_put_contents($filepath, $content);
        echo "    OK - Fichier nettoye\n";
    } else {
        echo "    OK - Aucune modification\n";
    }
}

echo "\nTermine !\n";
