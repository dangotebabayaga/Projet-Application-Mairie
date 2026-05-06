#!/usr/bin/env php
<?php
/**
 * SCRIPT : Ajouter automatiquement TOUTES les relations entre entités
 *
 * Usage: php add_all_relations.php
 *
 * Ce script modifie automatiquement les 19 entités pour remplacer
 * les colonnes *_id par des relations Doctrine ManyToOne/OneToMany
 */

$entitiesDir = __DIR__ . '/src/Entity';

echo "🔗 Ajout automatique des relations entre entités...\n\n";

// ============================================================================
// FONCTIONS UTILITAIRES
// ============================================================================

function addImportIfMissing(&$content, $import) {
    if (strpos($content, $import) === false) {
        $content = str_replace(
            'use Doctrine\ORM\Mapping as ORM;',
            "use Doctrine\ORM\Mapping as ORM;\n$import",
            $content
        );
    }
}

function removeProperty(&$content, $propertyName) {
    // Supprimer l'attribut ORM\Column et la propriété
    $content = preg_replace(
        '/#\[ORM\\\\Column\(name: \'' . $propertyName . '\'[^\]]*\]\]\s+private \?int \$\w+ = null;\s*/s',
        '',
        $content
    );
}

function removeGetterSetter(&$content, $methodPattern) {
    $content = preg_replace(
        '/public function ' . $methodPattern . '\([^)]*\)[^{]*\{[^}]*\}\s*/s',
        '',
        $content
    );
}

function addRelationProperty(&$content, $relationCode, $beforeLastBrace = false) {
    if ($beforeLastBrace) {
        // Ajouter avant le dernier }
        $content = preg_replace('/}\s*$/', "\n" . $relationCode . "\n}", $content);
    } else {
        // Ajouter après les propriétés existantes (avant les getters)
        $content = preg_replace(
            '/(private \?\w+ \$\w+ = null;\s*\n)(\s*public function)/s',
            "$1\n" . $relationCode . "\n$2",
            $content
        );
    }
}

// ============================================================================
// 1. VILLE - OneToMany vers tout le monde
// ============================================================================
echo "📝 [1/19] Ville.php...\n";

$villeFile = $entitiesDir . '/Ville.php';
$villeContent = file_get_contents($villeFile);

addImportIfMissing($villeContent, 'use Doctrine\Common\Collections\ArrayCollection;');
addImportIfMissing($villeContent, 'use Doctrine\Common\Collections\Collection;');

$villeRelations = '
    #[ORM\OneToMany(mappedBy: \'ville\', targetEntity: Quartier::class)]
    private Collection $quartiers;

    #[ORM\OneToMany(mappedBy: \'ville\', targetEntity: CategoriesCitoyen::class)]
    private Collection $categoriesCitoyens;

    #[ORM\OneToMany(mappedBy: \'ville\', targetEntity: ThematiquesEvenement::class)]
    private Collection $thematiquesEvenements;

    #[ORM\OneToMany(mappedBy: \'ville\', targetEntity: Utilisateur::class)]
    private Collection $utilisateurs;

    #[ORM\OneToMany(mappedBy: \'ville\', targetEntity: Sondage::class)]
    private Collection $sondages;

    #[ORM\OneToMany(mappedBy: \'ville\', targetEntity: Evenement::class)]
    private Collection $evenements;

    #[ORM\OneToMany(mappedBy: \'ville\', targetEntity: Actualite::class)]
    private Collection $actualites;

    #[ORM\OneToMany(mappedBy: \'ville\', targetEntity: ReseauSocial::class)]
    private Collection $reseauxSociaux;

    public function __construct()
    {
        $this->quartiers = new ArrayCollection();
        $this->categoriesCitoyens = new ArrayCollection();
        $this->thematiquesEvenements = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->sondages = new ArrayCollection();
        $this->evenements = new ArrayCollection();
        $this->actualites = new ArrayCollection();
        $this->reseauxSociaux = new ArrayCollection();
    }

    public function getQuartiers(): Collection { return $this->quartiers; }
    public function getCategoriesCitoyens(): Collection { return $this->categoriesCitoyens; }
    public function getThematiquesEvenements(): Collection { return $this->thematiquesEvenements; }
    public function getUtilisateurs(): Collection { return $this->utilisateurs; }
    public function getSondages(): Collection { return $this->sondages; }
    public function getEvenements(): Collection { return $this->evenements; }
    public function getActualites(): Collection { return $this->actualites; }
    public function getReseauxSociaux(): Collection { return $this->reseauxSociaux; }
';

addRelationProperty($villeContent, $villeRelations, true);
file_put_contents($villeFile, $villeContent);
echo "  ✅ Ville.php mis à jour\n";

// ============================================================================
// 2. QUARTIER - ManyToOne vers Ville
// ============================================================================
echo "📝 [2/19] Quartier.php...\n";

$quartierFile = $entitiesDir . '/Quartier.php';
$quartierContent = file_get_contents($quartierFile);

removeProperty($quartierContent, 'ville_id');
removeGetterSetter($quartierContent, 'getVilleId');
removeGetterSetter($quartierContent, 'setVilleId');

$quartierRelation = '
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: \'quartiers\')]
    #[ORM\JoinColumn(name: \'ville_id\', nullable: false)]
    private ?Ville $ville = null;

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): static { $this->ville = $ville; return $this; }
';

addRelationProperty($quartierContent, $quartierRelation, true);
file_put_contents($quartierFile, $quartierContent);
echo "  ✅ Quartier.php mis à jour\n";

// ============================================================================
// 3. CATEGORIES_CITOYEN - ManyToOne vers Ville
// ============================================================================
echo "📝 [3/19] CategoriesCitoyen.php...\n";

$categorieFile = $entitiesDir . '/CategoriesCitoyen.php';
$categorieContent = file_get_contents($categorieFile);

removeProperty($categorieContent, 'ville_id');
removeGetterSetter($categorieContent, 'getVilleId');
removeGetterSetter($categorieContent, 'setVilleId');

$categorieRelation = '
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: \'categoriesCitoyens\')]
    #[ORM\JoinColumn(name: \'ville_id\', nullable: false)]
    private ?Ville $ville = null;

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): static { $this->ville = $ville; return $this; }
';

addRelationProperty($categorieContent, $categorieRelation, true);
file_put_contents($categorieFile, $categorieContent);
echo "  ✅ CategoriesCitoyen.php mis à jour\n";

// ============================================================================
// 4. THEMATIQUES_EVENEMENT - ManyToOne vers Ville
// ============================================================================
echo "📝 [4/19] ThematiquesEvenement.php...\n";

$thematiqueFile = $entitiesDir . '/ThematiquesEvenement.php';
$thematiqueContent = file_get_contents($thematiqueFile);

removeProperty($thematiqueContent, 'ville_id');
removeGetterSetter($thematiqueContent, 'getVilleId');
removeGetterSetter($thematiqueContent, 'setVilleId');

$thematiqueRelation = '
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: \'thematiquesEvenements\')]
    #[ORM\JoinColumn(name: \'ville_id\', nullable: false)]
    private ?Ville $ville = null;

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): static { $this->ville = $ville; return $this; }
';

addRelationProperty($thematiqueContent, $thematiqueRelation, true);
file_put_contents($thematiqueFile, $thematiqueContent);
echo "  ✅ ThematiquesEvenement.php mis à jour\n";

// ============================================================================
// 5. UTILISATEUR - ManyToOne vers Ville, Quartier, Categorie
// ============================================================================
echo "📝 [5/19] Utilisateur.php...\n";

$utilisateurFile = $entitiesDir . '/Utilisateur.php';
$utilisateurContent = file_get_contents($utilisateurFile);

removeProperty($utilisateurContent, 'ville_id');
removeProperty($utilisateurContent, 'quartier_id');
removeProperty($utilisateurContent, 'categorie_id');
removeGetterSetter($utilisateurContent, 'getVilleId');
removeGetterSetter($utilisateurContent, 'setVilleId');
removeGetterSetter($utilisateurContent, 'getQuartierId');
removeGetterSetter($utilisateurContent, 'setQuartierId');
removeGetterSetter($utilisateurContent, 'getCategorieId');
removeGetterSetter($utilisateurContent, 'setCategorieId');

$utilisateurRelations = '
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: \'utilisateurs\')]
    #[ORM\JoinColumn(name: \'ville_id\', nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(targetEntity: Quartier::class)]
    #[ORM\JoinColumn(name: \'quartier_id\', nullable: true)]
    private ?Quartier $quartier = null;

    #[ORM\ManyToOne(targetEntity: CategoriesCitoyen::class)]
    #[ORM\JoinColumn(name: \'categorie_id\', nullable: true)]
    private ?CategoriesCitoyen $categorie = null;

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): static { $this->ville = $ville; return $this; }

    public function getQuartier(): ?Quartier { return $this->quartier; }
    public function setQuartier(?Quartier $quartier): static { $this->quartier = $quartier; return $this; }

    public function getCategorie(): ?CategoriesCitoyen { return $this->categorie; }
    public function setCategorie(?CategoriesCitoyen $categorie): static { $this->categorie = $categorie; return $this; }
';

addRelationProperty($utilisateurContent, $utilisateurRelations, true);
file_put_contents($utilisateurFile, $utilisateurContent);
echo "  ✅ Utilisateur.php mis à jour\n";

// ============================================================================
// 6. FICHIER - ManyToOne vers Utilisateur
// ============================================================================
echo "📝 [6/19] Fichier.php...\n";

$fichierFile = $entitiesDir . '/Fichier.php';
$fichierContent = file_get_contents($fichierFile);

removeProperty($fichierContent, 'uploade_par');
removeGetterSetter($fichierContent, 'getUploadePar');
removeGetterSetter($fichierContent, 'setUploadePar');

$fichierRelation = '
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'uploade_par\', nullable: false)]
    private ?Utilisateur $uploadePar = null;

    public function getUploadePar(): ?Utilisateur { return $this->uploadePar; }
    public function setUploadePar(?Utilisateur $uploadePar): static { $this->uploadePar = $uploadePar; return $this; }
';

addRelationProperty($fichierContent, $fichierRelation, true);
file_put_contents($fichierFile, $fichierContent);
echo "  ✅ Fichier.php mis à jour\n";

// ============================================================================
// 7. SIGNALEMENT - ManyToOne vers Fichier, Utilisateur, Quartier
// ============================================================================
echo "📝 [7/19] Signalement.php...\n";

$signalementFile = $entitiesDir . '/Signalement.php';
$signalementContent = file_get_contents($signalementFile);

removeProperty($signalementContent, 'fichier_id');
removeProperty($signalementContent, 'citoyen_id');
removeProperty($signalementContent, 'quartier_id');
removeGetterSetter($signalementContent, 'getFichierId');
removeGetterSetter($signalementContent, 'setFichierId');
removeGetterSetter($signalementContent, 'getCitoyenId');
removeGetterSetter($signalementContent, 'setCitoyenId');
removeGetterSetter($signalementContent, 'getQuartierId');
removeGetterSetter($signalementContent, 'setQuartierId');

$signalementRelations = '
    #[ORM\ManyToOne(targetEntity: Fichier::class)]
    #[ORM\JoinColumn(name: \'fichier_id\', nullable: true)]
    private ?Fichier $fichier = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'citoyen_id\', nullable: false)]
    private ?Utilisateur $citoyen = null;

    #[ORM\ManyToOne(targetEntity: Quartier::class)]
    #[ORM\JoinColumn(name: \'quartier_id\', nullable: true)]
    private ?Quartier $quartier = null;

    public function getFichier(): ?Fichier { return $this->fichier; }
    public function setFichier(?Fichier $fichier): static { $this->fichier = $fichier; return $this; }

    public function getCitoyen(): ?Utilisateur { return $this->citoyen; }
    public function setCitoyen(?Utilisateur $citoyen): static { $this->citoyen = $citoyen; return $this; }

    public function getQuartier(): ?Quartier { return $this->quartier; }
    public function setQuartier(?Quartier $quartier): static { $this->quartier = $quartier; return $this; }
';

addRelationProperty($signalementContent, $signalementRelations, true);
file_put_contents($signalementFile, $signalementContent);
echo "  ✅ Signalement.php mis à jour\n";

// ============================================================================
// 8. HISTORIQUE_SIGNALEMENT - ManyToOne vers Signalement, Utilisateur
// ============================================================================
echo "📝 [8/19] HistoriqueSignalement.php...\n";

$historiqueFile = $entitiesDir . '/HistoriqueSignalement.php';
$historiqueContent = file_get_contents($historiqueFile);

removeProperty($historiqueContent, 'signalement_id');
removeProperty($historiqueContent, 'modifie_par');
removeGetterSetter($historiqueContent, 'getSignalementId');
removeGetterSetter($historiqueContent, 'setSignalementId');
removeGetterSetter($historiqueContent, 'getModifiePar');
removeGetterSetter($historiqueContent, 'setModifiePar');

$historiqueRelations = '
    #[ORM\ManyToOne(targetEntity: Signalement::class)]
    #[ORM\JoinColumn(name: \'signalement_id\', nullable: false)]
    private ?Signalement $signalement = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'modifie_par\', nullable: true)]
    private ?Utilisateur $modifiePar = null;

    public function getSignalement(): ?Signalement { return $this->signalement; }
    public function setSignalement(?Signalement $signalement): static { $this->signalement = $signalement; return $this; }

    public function getModifiePar(): ?Utilisateur { return $this->modifiePar; }
    public function setModifiePar(?Utilisateur $modifiePar): static { $this->modifiePar = $modifiePar; return $this; }
';

addRelationProperty($historiqueContent, $historiqueRelations, true);
file_put_contents($historiqueFile, $historiqueContent);
echo "  ✅ HistoriqueSignalement.php mis à jour\n";

// ============================================================================
// 9. COMMENTAIRES_SIGNALEMENT - ManyToOne vers Signalement, Utilisateur
// ============================================================================
echo "📝 [9/19] CommentairesSignalement.php...\n";

$commentaireFile = $entitiesDir . '/CommentairesSignalement.php';
$commentaireContent = file_get_contents($commentaireFile);

removeProperty($commentaireContent, 'signalement_id');
removeProperty($commentaireContent, 'auteur_id');
removeGetterSetter($commentaireContent, 'getSignalementId');
removeGetterSetter($commentaireContent, 'setSignalementId');
removeGetterSetter($commentaireContent, 'getAuteurId');
removeGetterSetter($commentaireContent, 'setAuteurId');

$commentaireRelations = '
    #[ORM\ManyToOne(targetEntity: Signalement::class)]
    #[ORM\JoinColumn(name: \'signalement_id\', nullable: false)]
    private ?Signalement $signalement = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'auteur_id\', nullable: false)]
    private ?Utilisateur $auteur = null;

    public function getSignalement(): ?Signalement { return $this->signalement; }
    public function setSignalement(?Signalement $signalement): static { $this->signalement = $signalement; return $this; }

    public function getAuteur(): ?Utilisateur { return $this->auteur; }
    public function setAuteur(?Utilisateur $auteur): static { $this->auteur = $auteur; return $this; }
';

addRelationProperty($commentaireContent, $commentaireRelations, true);
file_put_contents($commentaireFile, $commentaireContent);
echo "  ✅ CommentairesSignalement.php mis à jour\n";

// ============================================================================
// 10. SONDAGE - ManyToOne vers Ville, Quartier, Categorie, Utilisateur
// ============================================================================
echo "📝 [10/19] Sondage.php...\n";

$sondageFile = $entitiesDir . '/Sondage.php';
$sondageContent = file_get_contents($sondageFile);

removeProperty($sondageContent, 'ville_id');
removeProperty($sondageContent, 'quartier_id');
removeProperty($sondageContent, 'categorie_id');
removeProperty($sondageContent, 'cree_par');
removeGetterSetter($sondageContent, 'getVilleId');
removeGetterSetter($sondageContent, 'setVilleId');
removeGetterSetter($sondageContent, 'getQuartierId');
removeGetterSetter($sondageContent, 'setQuartierId');
removeGetterSetter($sondageContent, 'getCategorieId');
removeGetterSetter($sondageContent, 'setCategorieId');
removeGetterSetter($sondageContent, 'getCreePar');
removeGetterSetter($sondageContent, 'setCreePar');

$sondageRelations = '
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: \'sondages\')]
    #[ORM\JoinColumn(name: \'ville_id\', nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(targetEntity: Quartier::class)]
    #[ORM\JoinColumn(name: \'quartier_id\', nullable: true)]
    private ?Quartier $quartier = null;

    #[ORM\ManyToOne(targetEntity: CategoriesCitoyen::class)]
    #[ORM\JoinColumn(name: \'categorie_id\', nullable: true)]
    private ?CategoriesCitoyen $categorie = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'cree_par\', nullable: false)]
    private ?Utilisateur $creePar = null;

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): static { $this->ville = $ville; return $this; }

    public function getQuartier(): ?Quartier { return $this->quartier; }
    public function setQuartier(?Quartier $quartier): static { $this->quartier = $quartier; return $this; }

    public function getCategorie(): ?CategoriesCitoyen { return $this->categorie; }
    public function setCategorie(?CategoriesCitoyen $categorie): static { $this->categorie = $categorie; return $this; }

    public function getCreePar(): ?Utilisateur { return $this->creePar; }
    public function setCreePar(?Utilisateur $creePar): static { $this->creePar = $creePar; return $this; }
';

addRelationProperty($sondageContent, $sondageRelations, true);
file_put_contents($sondageFile, $sondageContent);
echo "  ✅ Sondage.php mis à jour\n";

// ============================================================================
// 11. QUESTIONS_SONDAGE - ManyToOne vers Sondage
// ============================================================================
echo "📝 [11/19] QuestionsSondage.php...\n";

$questionFile = $entitiesDir . '/QuestionsSondage.php';
$questionContent = file_get_contents($questionFile);

removeProperty($questionContent, 'sondage_id');
removeGetterSetter($questionContent, 'getSondageId');
removeGetterSetter($questionContent, 'setSondageId');

$questionRelation = '
    #[ORM\ManyToOne(targetEntity: Sondage::class)]
    #[ORM\JoinColumn(name: \'sondage_id\', nullable: false)]
    private ?Sondage $sondage = null;

    public function getSondage(): ?Sondage { return $this->sondage; }
    public function setSondage(?Sondage $sondage): static { $this->sondage = $sondage; return $this; }
';

addRelationProperty($questionContent, $questionRelation, true);
file_put_contents($questionFile, $questionContent);
echo "  ✅ QuestionsSondage.php mis à jour\n";

// ============================================================================
// 12. REPONSES_SONDAGE - ManyToOne vers QuestionsSondage
// ============================================================================
echo "📝 [12/19] ReponsesSondage.php...\n";

$reponseFile = $entitiesDir . '/ReponsesSondage.php';
$reponseContent = file_get_contents($reponseFile);

removeProperty($reponseContent, 'question_id');
removeGetterSetter($reponseContent, 'getQuestionId');
removeGetterSetter($reponseContent, 'setQuestionId');

$reponseRelation = '
    #[ORM\ManyToOne(targetEntity: QuestionsSondage::class)]
    #[ORM\JoinColumn(name: \'question_id\', nullable: false)]
    private ?QuestionsSondage $question = null;

    public function getQuestion(): ?QuestionsSondage { return $this->question; }
    public function setQuestion(?QuestionsSondage $question): static { $this->question = $question; return $this; }
';

addRelationProperty($reponseContent, $reponseRelation, true);
file_put_contents($reponseFile, $reponseContent);
echo "  ✅ ReponsesSondage.php mis à jour\n";

// ============================================================================
// 13. VOTES_SONDAGE - ManyToOne vers Sondage, Utilisateur, Question, Reponse
// ============================================================================
echo "📝 [13/19] VotesSondage.php...\n";

$voteFile = $entitiesDir . '/VotesSondage.php';
$voteContent = file_get_contents($voteFile);

removeProperty($voteContent, 'sondage_id');
removeProperty($voteContent, 'citoyen_id');
removeProperty($voteContent, 'question_id');
removeProperty($voteContent, 'reponse_id');
removeGetterSetter($voteContent, 'getSondageId');
removeGetterSetter($voteContent, 'setSondageId');
removeGetterSetter($voteContent, 'getCitoyenId');
removeGetterSetter($voteContent, 'setCitoyenId');
removeGetterSetter($voteContent, 'getQuestionId');
removeGetterSetter($voteContent, 'setQuestionId');
removeGetterSetter($voteContent, 'getReponseId');
removeGetterSetter($voteContent, 'setReponseId');

$voteRelations = '
    #[ORM\ManyToOne(targetEntity: Sondage::class)]
    #[ORM\JoinColumn(name: \'sondage_id\', nullable: false)]
    private ?Sondage $sondage = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'citoyen_id\', nullable: false)]
    private ?Utilisateur $citoyen = null;

    #[ORM\ManyToOne(targetEntity: QuestionsSondage::class)]
    #[ORM\JoinColumn(name: \'question_id\', nullable: false)]
    private ?QuestionsSondage $question = null;

    #[ORM\ManyToOne(targetEntity: ReponsesSondage::class)]
    #[ORM\JoinColumn(name: \'reponse_id\', nullable: false)]
    private ?ReponsesSondage $reponse = null;

    public function getSondage(): ?Sondage { return $this->sondage; }
    public function setSondage(?Sondage $sondage): static { $this->sondage = $sondage; return $this; }

    public function getCitoyen(): ?Utilisateur { return $this->citoyen; }
    public function setCitoyen(?Utilisateur $citoyen): static { $this->citoyen = $citoyen; return $this; }

    public function getQuestion(): ?QuestionsSondage { return $this->question; }
    public function setQuestion(?QuestionsSondage $question): static { $this->question = $question; return $this; }

    public function getReponse(): ?ReponsesSondage { return $this->reponse; }
    public function setReponse(?ReponsesSondage $reponse): static { $this->reponse = $reponse; return $this; }
';

addRelationProperty($voteContent, $voteRelations, true);
file_put_contents($voteFile, $voteContent);
echo "  ✅ VotesSondage.php mis à jour\n";

// ============================================================================
// 14. EVENEMENT - ManyToOne vers ThematiquesEvenement, Ville, Utilisateur
// ============================================================================
echo "📝 [14/19] Evenement.php...\n";

$evenementFile = $entitiesDir . '/Evenement.php';
$evenementContent = file_get_contents($evenementFile);

removeProperty($evenementContent, 'thematique_id');
removeProperty($evenementContent, 'ville_id');
removeProperty($evenementContent, 'cree_par');
removeGetterSetter($evenementContent, 'getThematiqueId');
removeGetterSetter($evenementContent, 'setThematiqueId');
removeGetterSetter($evenementContent, 'getVilleId');
removeGetterSetter($evenementContent, 'setVilleId');
removeGetterSetter($evenementContent, 'getCreePar');
removeGetterSetter($evenementContent, 'setCreePar');

$evenementRelations = '
    #[ORM\ManyToOne(targetEntity: ThematiquesEvenement::class)]
    #[ORM\JoinColumn(name: \'thematique_id\', nullable: false)]
    private ?ThematiquesEvenement $thematique = null;

    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: \'evenements\')]
    #[ORM\JoinColumn(name: \'ville_id\', nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'cree_par\', nullable: false)]
    private ?Utilisateur $creePar = null;

    public function getThematique(): ?ThematiquesEvenement { return $this->thematique; }
    public function setThematique(?ThematiquesEvenement $thematique): static { $this->thematique = $thematique; return $this; }

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): static { $this->ville = $ville; return $this; }

    public function getCreePar(): ?Utilisateur { return $this->creePar; }
    public function setCreePar(?Utilisateur $creePar): static { $this->creePar = $creePar; return $this; }
';

addRelationProperty($evenementContent, $evenementRelations, true);
file_put_contents($evenementFile, $evenementContent);
echo "  ✅ Evenement.php mis à jour\n";

// ============================================================================
// 15. EVENEMENTS_UTILISATEUR - ManyToOne vers Utilisateur, Evenement
// ============================================================================
echo "📝 [15/19] EvenementsUtilisateur.php...\n";

$evUserFile = $entitiesDir . '/EvenementsUtilisateur.php';
$evUserContent = file_get_contents($evUserFile);

removeProperty($evUserContent, 'utilisateur_id');
removeProperty($evUserContent, 'evenement_id');
removeGetterSetter($evUserContent, 'getUtilisateurId');
removeGetterSetter($evUserContent, 'setUtilisateurId');
removeGetterSetter($evUserContent, 'getEvenementId');
removeGetterSetter($evUserContent, 'setEvenementId');

$evUserRelations = '
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'utilisateur_id\', nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Evenement::class)]
    #[ORM\JoinColumn(name: \'evenement_id\', nullable: false)]
    private ?Evenement $evenement = null;

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getEvenement(): ?Evenement { return $this->evenement; }
    public function setEvenement(?Evenement $evenement): static { $this->evenement = $evenement; return $this; }
';

addRelationProperty($evUserContent, $evUserRelations, true);
file_put_contents($evUserFile, $evUserContent);
echo "  ✅ EvenementsUtilisateur.php mis à jour\n";

// ============================================================================
// 16. ACTUALITE - ManyToOne vers Ville, Utilisateur
// ============================================================================
echo "📝 [16/19] Actualite.php...\n";

$actualiteFile = $entitiesDir . '/Actualite.php';
$actualiteContent = file_get_contents($actualiteFile);

removeProperty($actualiteContent, 'ville_id');
removeProperty($actualiteContent, 'auteur_id');
removeGetterSetter($actualiteContent, 'getVilleId');
removeGetterSetter($actualiteContent, 'setVilleId');
removeGetterSetter($actualiteContent, 'getAuteurId');
removeGetterSetter($actualiteContent, 'setAuteurId');

$actualiteRelations = '
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: \'actualites\')]
    #[ORM\JoinColumn(name: \'ville_id\', nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'auteur_id\', nullable: true)]
    private ?Utilisateur $auteur = null;

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): static { $this->ville = $ville; return $this; }

    public function getAuteur(): ?Utilisateur { return $this->auteur; }
    public function setAuteur(?Utilisateur $auteur): static { $this->auteur = $auteur; return $this; }
';

addRelationProperty($actualiteContent, $actualiteRelations, true);
file_put_contents($actualiteFile, $actualiteContent);
echo "  ✅ Actualite.php mis à jour\n";

// ============================================================================
// 17. RESEAU_SOCIAL - ManyToOne vers Ville
// ============================================================================
echo "📝 [17/19] ReseauSocial.php...\n";

$reseauFile = $entitiesDir . '/ReseauSocial.php';
$reseauContent = file_get_contents($reseauFile);

removeProperty($reseauContent, 'ville_id');
removeGetterSetter($reseauContent, 'getVilleId');
removeGetterSetter($reseauContent, 'setVilleId');

$reseauRelation = '
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: \'reseauxSociaux\')]
    #[ORM\JoinColumn(name: \'ville_id\', nullable: false)]
    private ?Ville $ville = null;

    public function getVille(): ?Ville { return $this->ville; }
    public function setVille(?Ville $ville): static { $this->ville = $ville; return $this; }
';

addRelationProperty($reseauContent, $reseauRelation, true);
file_put_contents($reseauFile, $reseauContent);
echo "  ✅ ReseauSocial.php mis à jour\n";

// ============================================================================
// 18. NOTIFICATION - ManyToOne vers Utilisateur
// ============================================================================
echo "📝 [18/19] Notification.php...\n";

$notificationFile = $entitiesDir . '/Notification.php';
$notificationContent = file_get_contents($notificationFile);

removeProperty($notificationContent, 'destinataire_id');
removeGetterSetter($notificationContent, 'getDestinataireId');
removeGetterSetter($notificationContent, 'setDestinataireId');

$notificationRelation = '
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'destinataire_id\', nullable: false)]
    private ?Utilisateur $destinataire = null;

    public function getDestinataire(): ?Utilisateur { return $this->destinataire; }
    public function setDestinataire(?Utilisateur $destinataire): static { $this->destinataire = $destinataire; return $this; }
';

addRelationProperty($notificationContent, $notificationRelation, true);
file_put_contents($notificationFile, $notificationContent);
echo "  ✅ Notification.php mis à jour\n";

// ============================================================================
// 19. LOG_ACTION - ManyToOne vers Utilisateur
// ============================================================================
echo "📝 [19/19] LogAction.php...\n";

$logFile = $entitiesDir . '/LogAction.php';
$logContent = file_get_contents($logFile);

removeProperty($logContent, 'utilisateur_id');
removeGetterSetter($logContent, 'getUtilisateurId');
removeGetterSetter($logContent, 'setUtilisateurId');

$logRelation = '
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: \'utilisateur_id\', nullable: true)]
    private ?Utilisateur $utilisateur = null;

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }
';

addRelationProperty($logContent, $logRelation, true);
file_put_contents($logFile, $logContent);
echo "  ✅ LogAction.php mis à jour\n";

// ============================================================================
// FIN
// ============================================================================

echo "\n🎉 Terminé ! Toutes les relations ont été ajoutées aux 19 entités.\n\n";
echo "📝 Prochaines étapes :\n";
echo "  1. Vérifier que Doctrine reconnaît les relations :\n";
echo "     docker-compose exec symfony php bin/console doctrine:mapping:info\n\n";
echo "  2. Valider le schéma complet :\n";
echo "     docker-compose exec symfony php bin/console doctrine:schema:validate\n\n";
echo "  3. Si nécessaire, créer une migration vide et la marquer comme exécutée :\n";
echo "     docker-compose exec symfony php bin/console make:migration\n";
echo "     docker-compose exec symfony php bin/console doctrine:migrations:version --add --all\n\n";
