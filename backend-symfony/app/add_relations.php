#!/usr/bin/env php
<?php
/**
 * SCRIPT : Ajouter les relations entre entités
 *
 * Usage: php add_relations.php
 */

$entitiesDir = __DIR__ . '/src/Entity';

echo "🔗 Ajout des relations entre entités...\n\n";

// ============================================================================
// 1. VILLE - Relations OneToMany
// ============================================================================
echo "  📝 Ajout des relations dans Ville...\n";

$villeFile = $entitiesDir . '/Ville.php';
$villeContent = file_get_contents($villeFile);

// Ajouter les imports
if (strpos($villeContent, 'use Doctrine\Common\Collections\ArrayCollection;') === false) {
    $villeContent = str_replace(
        'use Doctrine\ORM\Mapping as ORM;',
        "use Doctrine\ORM\Mapping as ORM;\nuse Doctrine\Common\Collections\ArrayCollection;\nuse Doctrine\Common\Collections\Collection;",
        $villeContent
    );
}

// Ajouter les propriétés de collection AVANT le dernier "}"
$relations = "
    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Quartier::class, cascade: ['persist'])]
    private Collection \$quartiers;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: CategoriesCitoyen::class, cascade: ['persist'])]
    private Collection \$categoriesCitoyens;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: ThematiquesEvenement::class, cascade: ['persist'])]
    private Collection \$thematiquesEvenements;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Utilisateur::class)]
    private Collection \$utilisateurs;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Sondage::class)]
    private Collection \$sondages;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Evenement::class)]
    private Collection \$evenements;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Actualite::class)]
    private Collection \$actualites;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: ReseauSocial::class, cascade: ['persist', 'remove'])]
    private Collection \$reseauxSociaux;

    public function __construct()
    {
        \$this->quartiers = new ArrayCollection();
        \$this->categoriesCitoyens = new ArrayCollection();
        \$this->thematiquesEvenements = new ArrayCollection();
        \$this->utilisateurs = new ArrayCollection();
        \$this->sondages = new ArrayCollection();
        \$this->evenements = new ArrayCollection();
        \$this->actualites = new ArrayCollection();
        \$this->reseauxSociaux = new ArrayCollection();
    }

    // Getters pour les collections
    public function getQuartiers(): Collection { return \$this->quartiers; }
    public function getCategoriesCitoyens(): Collection { return \$this->categoriesCitoyens; }
    public function getThematiquesEvenements(): Collection { return \$this->thematiquesEvenements; }
    public function getUtilisateurs(): Collection { return \$this->utilisateurs; }
    public function getSondages(): Collection { return \$this->sondages; }
    public function getEvenements(): Collection { return \$this->evenements; }
    public function getActualites(): Collection { return \$this->actualites; }
    public function getReseauxSociaux(): Collection { return \$this->reseauxSociaux; }
";

// Insérer avant le dernier "}"
$villeContent = preg_replace('/}\s*$/', $relations . "\n}", $villeContent);
file_put_contents($villeFile, $villeContent);

echo "    ✅ Ville.php mis à jour\n";

// ============================================================================
// 2. QUARTIER - Relation ManyToOne vers Ville
// ============================================================================
echo "  📝 Ajout des relations dans Quartier...\n";

$quartierFile = $entitiesDir . '/Quartier.php';
$quartierContent = file_get_contents($quartierFile);

// Remplacer la colonne ville_id par une relation
$quartierContent = preg_replace(
    '/#\[ORM\\\\Column\(name: \'ville_id\'.+?\]\]\s+private \?int \$villeId = null;/s',
    "#[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'quartiers')]\n    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]\n    private ?Ville \$ville = null;",
    $quartierContent
);

// Ajouter getter/setter pour ville
$quartierContent = preg_replace(
    '/public function getVilleId\(\).+?}\s+public function setVilleId\(.+?}\s*/s',
    "public function getVille(): ?Ville { return \$this->ville; }\n    public function setVille(?Ville \$ville): static { \$this->ville = \$ville; return \$this; }\n    ",
    $quartierContent
);

file_put_contents($quartierFile, $quartierContent);
echo "    ✅ Quartier.php mis à jour\n";

// ============================================================================
// 3. CATEGORIES_CITOYEN - Relation ManyToOne vers Ville
// ============================================================================
echo "  📝 Ajout des relations dans CategoriesCitoyen...\n";

$categorieFile = $entitiesDir . '/CategoriesCitoyen.php';
$categorieContent = file_get_contents($categorieFile);

$categorieContent = preg_replace(
    '/#\[ORM\\\\Column\(name: \'ville_id\'.+?\]\]\s+private \?int \$villeId = null;/s',
    "#[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'categoriesCitoyens')]\n    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]\n    private ?Ville \$ville = null;",
    $categorieContent
);

$categorieContent = preg_replace(
    '/public function getVilleId\(\).+?}\s+public function setVilleId\(.+?}\s*/s',
    "public function getVille(): ?Ville { return \$this->ville; }\n    public function setVille(?Ville \$ville): static { \$this->ville = \$ville; return \$this; }\n    ",
    $categorieContent
);

file_put_contents($categorieFile, $categorieContent);
echo "    ✅ CategoriesCitoyen.php mis à jour\n";

// ============================================================================
// 4. THEMATIQUES_EVENEMENT - Relation ManyToOne vers Ville
// ============================================================================
echo "  📝 Ajout des relations dans ThematiquesEvenement...\n";

$thematiqueFile = $entitiesDir . '/ThematiquesEvenement.php';
$thematiqueContent = file_get_contents($thematiqueFile);

$thematiqueContent = preg_replace(
    '/#\[ORM\\\\Column\(name: \'ville_id\'.+?\]\]\s+private \?int \$villeId = null;/s',
    "#[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'thematiquesEvenements')]\n    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]\n    private ?Ville \$ville = null;",
    $thematiqueContent
);

$thematiqueContent = preg_replace(
    '/public function getVilleId\(\).+?}\s+public function setVilleId\(.+?}\s*/s',
    "public function getVille(): ?Ville { return \$this->ville; }\n    public function setVille(?Ville \$ville): static { \$this->ville = \$ville; return \$this; }\n    ",
    $thematiqueContent
);

file_put_contents($thematiqueFile, $thematiqueContent);
echo "    ✅ ThematiquesEvenement.php mis à jour\n";

// ============================================================================
// 5. UTILISATEUR - Relations ManyToOne
// ============================================================================
echo "  📝 Ajout des relations dans Utilisateur...\n";

$utilisateurFile = $entitiesDir . '/Utilisateur.php';
$utilisateurContent = file_get_contents($utilisateurFile);

// Ville
$utilisateurContent = preg_replace(
    '/#\[ORM\\\\Column\(name: \'ville_id\'.+?\]\]\s+private \?int \$villeId = null;/s',
    "#[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'utilisateurs')]\n    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]\n    private ?Ville \$ville = null;",
    $utilisateurContent
);

// Quartier
$utilisateurContent = preg_replace(
    '/#\[ORM\\\\Column\(name: \'quartier_id\'.+?\]\]\s+private \?int \$quartierId = null;/s',
    "#[ORM\ManyToOne(targetEntity: Quartier::class)]\n    #[ORM\JoinColumn(name: 'quartier_id', nullable: true)]\n    private ?Quartier \$quartier = null;",
    $utilisateurContent
);

// Categorie
$utilisateurContent = preg_replace(
    '/#\[ORM\\\\Column\(name: \'categorie_id\'.+?\]\]\s+private \?int \$categorieId = null;/s',
    "#[ORM\ManyToOne(targetEntity: CategoriesCitoyen::class)]\n    #[ORM\JoinColumn(name: 'categorie_id', nullable: true)]\n    private ?CategoriesCitoyen \$categorie = null;",
    $utilisateurContent
);

// Remplacer les getters/setters
$utilisateurContent = preg_replace('/public function getVilleId\(\).+?}\s+public function setVilleId\(.+?}\s*/s', '', $utilisateurContent);
$utilisateurContent = preg_replace('/public function getQuartierId\(\).+?}\s+public function setQuartierId\(.+?}\s*/s', '', $utilisateurContent);
$utilisateurContent = preg_replace('/public function getCategorieId\(\).+?}\s+public function setCategorieId\(.+?}\s*/s', '', $utilisateurContent);

// Ajouter les nouveaux getters/setters avant le dernier }
$utilisateurRelations = "
    public function getVille(): ?Ville { return \$this->ville; }
    public function setVille(?Ville \$ville): static { \$this->ville = \$ville; return \$this; }

    public function getQuartier(): ?Quartier { return \$this->quartier; }
    public function setQuartier(?Quartier \$quartier): static { \$this->quartier = \$quartier; return \$this; }

    public function getCategorie(): ?CategoriesCitoyen { return \$this->categorie; }
    public function setCategorie(?CategoriesCitoyen \$categorie): static { \$this->categorie = \$categorie; return \$this; }
";

$utilisateurContent = preg_replace('/}\s*$/', $utilisateurRelations . "\n}", $utilisateurContent);
file_put_contents($utilisateurFile, $utilisateurContent);
echo "    ✅ Utilisateur.php mis à jour\n";

// ============================================================================
// 6. FICHIER - Relation ManyToOne vers Utilisateur
// ============================================================================
echo "  📝 Ajout des relations dans Fichier...\n";

$fichierFile = $entitiesDir . '/Fichier.php';
$fichierContent = file_get_contents($fichierFile);

$fichierContent = preg_replace(
    '/#\[ORM\\\\Column\(name: \'uploade_par\'.+?\]\]\s+private \?int \$uploadePar = null;/s',
    "#[ORM\ManyToOne(targetEntity: Utilisateur::class)]\n    #[ORM\JoinColumn(name: 'uploade_par', nullable: false)]\n    private ?Utilisateur \$uploadePar = null;",
    $fichierContent
);

$fichierContent = preg_replace(
    '/public function getUploadePar\(\).+?}\s+public function setUploadePar\(.+?}\s*/s',
    "public function getUploadePar(): ?Utilisateur { return \$this->uploadePar; }\n    public function setUploadePar(?Utilisateur \$uploadePar): static { \$this->uploadePar = \$uploadePar; return \$this; }\n    ",
    $fichierContent
);

file_put_contents($fichierFile, $fichierContent);
echo "    ✅ Fichier.php mis à jour\n";

// ============================================================================
// 7-19. Continuer pour les autres entités...
// ============================================================================

echo "\n🎉 Relations de base ajoutées !\n";
echo "⚠️  Vous devrez ajouter manuellement les relations complexes restantes\n";
echo "📝 Consultez la documentation : docs/GUIDE_SYMFONY_DOCTRINE.md\n";
