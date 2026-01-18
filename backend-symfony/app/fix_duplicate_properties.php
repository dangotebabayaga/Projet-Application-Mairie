#!/usr/bin/env php
<?php
/**
 * SCRIPT : Corriger les propriétés dupliquées dans les entités
 */

$entitiesDir = __DIR__ . '/src/Entity';

echo "🔧 Correction des propriétés dupliquées...\n\n";

// Liste des fichiers et propriétés à nettoyer
$fixes = [
    'Fichier.php' => ['uploade_par'],
    'Quartier.php' => ['ville_id'],
    'CategoriesCitoyen.php' => ['ville_id'],
    'ThematiquesEvenement.php' => ['ville_id'],
    'Utilisateur.php' => ['ville_id', 'quartier_id', 'categorie_id'],
    'Signalement.php' => ['fichier_id', 'citoyen_id', 'quartier_id'],
    'HistoriqueSignalement.php' => ['signalement_id', 'modifie_par'],
    'CommentairesSignalement.php' => ['signalement_id', 'auteur_id'],
    'Sondage.php' => ['ville_id', 'quartier_id', 'categorie_id', 'cree_par'],
    'QuestionsSondage.php' => ['sondage_id'],
    'ReponsesSondage.php' => ['question_id'],
    'VotesSondage.php' => ['sondage_id', 'citoyen_id', 'question_id', 'reponse_id'],
    'Evenement.php' => ['thematique_id', 'ville_id', 'cree_par'],
    'EvenementsUtilisateur.php' => ['utilisateur_id', 'evenement_id'],
    'Actualite.php' => ['ville_id', 'auteur_id'],
    'ReseauSocial.php' => ['ville_id'],
    'Notification.php' => ['destinataire_id'],
    'LogAction.php' => ['utilisateur_id'],
];

foreach ($fixes as $filename => $propertiesToRemove) {
    $filepath = $entitiesDir . '/' . $filename;

    if (!file_exists($filepath)) {
        echo "  ⚠️  $filename n'existe pas\n";
        continue;
    }

    echo "📝 $filename...\n";
    $content = file_get_contents($filepath);
    $originalContent = $content;

    foreach ($propertiesToRemove as $propName) {
        // Supprimer la déclaration de propriété avec #[ORM\Column]
        $pattern = '/#\[ORM\\\\Column\([^\]]*name:\s*[\'"]' . preg_quote($propName, '/') . '[\'"][^\]]*\)\]\s*private\s+\?int\s+\$\w+\s*=\s*null;\s*/s';
        $content = preg_replace($pattern, '', $content);

        // Alternative : version simple pour type: 'integer'
        $pattern2 = '/#\[ORM\\\\Column\([^\]]*[\'"]' . preg_quote($propName, '/') . '[\'"][^\]]*type:\s*[\'"]integer[\'"][^\]]*\)\]\s*private\s+\?int\s+\$\w+\s*=\s*null;\s*/s';
        $content = preg_replace($pattern2, '', $content);

        // Version encore plus simple : juste le nom de la colonne
        $pattern3 = '/#\[ORM\\\\Column\(name:\s*[\'"]' . preg_quote($propName, '/') . '[\'"],\s*type:\s*[\'"]integer[\'"][^\)]*\)\]\s*private\s+\?int\s+\$\w+\s*=\s*null;\s*/s';
        $content = preg_replace($pattern3, '', $content);
    }

    if ($content !== $originalContent) {
        file_put_contents($filepath, $content);
        echo "  ✅ Corrigé\n";
    } else {
        echo "  ℹ️  Aucune modification nécessaire\n";
    }
}

echo "\n🎉 Terminé !\n";
