#!/usr/bin/env python3
"""
Script pour nettoyer toutes les entités et supprimer les déclarations de propriétés dupliquées
"""

import re
import os

entities_dir = './src/Entity'

# Mapper les noms de colonnes vers les noms de propriétés
column_to_property = {
    'ville_id': 'ville',
    'quartier_id': 'quartier',
    'categorie_id': 'categorie',
    'fichier_id': 'fichier',
    'citoyen_id': 'citoyen',
    'signalement_id': 'signalement',
    'auteur_id': 'auteur',
    'destinataire_id': 'destinataire',
    'utilisateur_id': 'utilisateur',
    'sondage_id': 'sondage',
    'question_id': 'question',
    'reponse_id': 'reponse',
    'thematique_id': 'thematique',
    'evenement_id': 'evenement',
    'uploade_par': 'uploadePar',
    'modifie_par': 'modifiePar',
    'cree_par': 'creePar',
}

files_to_clean = [
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
]

for filename in files_to_clean:
    filepath = os.path.join(entities_dir, filename)

    if not os.path.exists(filepath):
        print(f"⚠️  {filename} n'existe pas")
        continue

    print(f"📝 Nettoyage de {filename}...")

    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content

    # Supprimer toutes les propriétés INT avec *_id ou *_par
    # Pattern pour trouver les colonnes comme:
    # #[ORM\Column(name: 'ville_id', type: 'integer')]
    # private ?int $villeId = null;
    pattern = r"#\[ORM\\Column\(name:\s*'([^']+)',\s*type:\s*'integer'\)\]\s*private\s+\?int\s+\$\w+\s*=\s*null;\s*"

    matches = re.findall(pattern, content)
    for match in matches:
        if match in column_to_property:
            # Supprimer cette déclaration
            full_pattern = r"#\[ORM\\Column\(name:\s*'" + re.escape(match) + r"',\s*type:\s*'integer'\)\]\s*private\s+\?int\s+\$\w+\s*=\s*null;\s*"
            content = re.sub(full_pattern, '', content)
            print(f"  ✅ Supprimé la propriété pour {match}")

    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"  ✅ Fichier mis à jour")
    else:
        print(f"  ℹ️  Aucune modification")

print("\n🎉 Nettoyage terminé !")
