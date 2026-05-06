# 🔗 GUIDE : Relations entre entités - À copier/coller

**Ce fichier contient toutes les relations à ajouter manuellement dans chaque entité.**

---

## 📋 **Instructions**

Pour chaque entité ci-dessous :
1. Ouvrir le fichier `backend-symfony/app/src/Entity/NomEntite.php`
2. Copier-coller le code fourni
3. Remplacer les colonnes `*_id` par les relations

---

## **1. Ville.php** - Ajouter les collections

**Ajouter après les imports :**
```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
```

**Ajouter après les propriétés existantes (avant les getters) :**
```php
    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Quartier::class)]
    private Collection $quartiers;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: CategoriesCitoyen::class)]
    private Collection $categoriesCitoyens;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: ThematiquesEvenement::class)]
    private Collection $thematiquesEvenements;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Utilisateur::class)]
    private Collection $Utilisateur;

    public function __construct()
    {
        $this->quartiers = new ArrayCollection();
        $this->categoriesCitoyens = new ArrayCollection();
        $this->thematiquesEvenements = new ArrayCollection();
        $this->Utilisateur = new ArrayCollection();
    }
```

**Ajouter les getters (avant le dernier `}`) :**
```php
    public function getQuartiers(): Collection
    {
        return $this->quartiers;
    }

    public function getCategoriesCitoyens(): Collection
    {
        return $this->categoriesCitoyens;
    }

    public function getThematiquesEvenements(): Collection
    {
        return $this->thematiquesEvenements;
    }

    public function getUtilisateur(): Collection
    {
        return $this->Utilisateur;
    }
```

---

## **2. Quartier.php** - Relation vers Ville

**SUPPRIMER ces lignes :**
```php
#[ORM\Column(name: 'ville_id', ...)]
private ?int $villeId = null;

public function getVilleId(): ?int { ... }
public function setVilleId(?int $villeId): static { ... }
```

**REMPLACER par :**
```php
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'quartiers')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): static
    {
        $this->ville = $ville;
        return $this;
    }
```

---

## **3. CategoriesCitoyen.php** - Relation vers Ville

**SUPPRIMER :**
```php
#[ORM\Column(name: 'ville_id', ...)]
private ?int $villeId = null;
// + getters/setters villeId
```

**REMPLACER par :**
```php
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'categoriesCitoyens')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): static
    {
        $this->ville = $ville;
        return $this;
    }
```

---

## **4. ThematiquesEvenement.php** - Relation vers Ville

**SUPPRIMER :**
```php
#[ORM\Column(name: 'ville_id', ...)]
private ?int $villeId = null;
// + getters/setters
```

**REMPLACER par :**
```php
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'thematiquesEvenements')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): static
    {
        $this->ville = $ville;
        return $this;
    }
```

---

## **5. Utilisateur.php** - Relations multiples

**SUPPRIMER :**
```php
#[ORM\Column(name: 'ville_id', ...)]
private ?int $villeId = null;

#[ORM\Column(name: 'quartier_id', ...)]
private ?int $quartierId = null;

#[ORM\Column(name: 'categorie_id', ...)]
private ?int $categorieId = null;

// + tous les getters/setters de ces 3 colonnes
```

**REMPLACER par :**
```php
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'Utilisateur')]
    #[ORM\JoinColumn(name: 'ville_id', nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(targetEntity: Quartier::class)]
    #[ORM\JoinColumn(name: 'quartier_id', nullable: true)]
    private ?Quartier $quartier = null;

    #[ORM\ManyToOne(targetEntity: CategoriesCitoyen::class)]
    #[ORM\JoinColumn(name: 'categorie_id', nullable: true)]
    private ?CategoriesCitoyen $categorie = null;

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): static
    {
        $this->quartier = $quartier;
        return $this;
    }

    public function getCategorie(): ?CategoriesCitoyen
    {
        return $this->categorie;
    }

    public function setCategorie(?CategoriesCitoyen $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }
```

---

## **6. Fichier.php** - Relation vers Utilisateur

**SUPPRIMER :**
```php
#[ORM\Column(name: 'uploade_par', ...)]
private ?int $uploadePar = null;
// + getters/setters
```

**REMPLACER par :**
```php
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'uploade_par', nullable: false)]
    private ?Utilisateur $uploadePar = null;

    public function getUploadePar(): ?Utilisateur
    {
        return $this->uploadePar;
    }

    public function setUploadePar(?Utilisateur $uploadePar): static
    {
        $this->uploadePar = $uploadePar;
        return $this;
    }
```

---

## **7. Signalement.php** - Relations multiples

**SUPPRIMER :**
```php
#[ORM\Column(name: 'fichier_id', ...)]
private ?int $fichierId = null;

#[ORM\Column(name: 'citoyen_id', ...)]
private ?int $citoyenId = null;

#[ORM\Column(name: 'quartier_id', ...)]
private ?int $quartierId = null;
// + getters/setters
```

**REMPLACER par :**
```php
    #[ORM\ManyToOne(targetEntity: Fichier::class)]
    #[ORM\JoinColumn(name: 'fichier_id', nullable: true)]
    private ?Fichier $fichier = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'citoyen_id', nullable: false)]
    private ?Utilisateur $citoyen = null;

    #[ORM\JoinColumn(name: 'quartier_id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Quartier::class)]
    private ?Quartier $quartier = null;

    public function getFichier(): ?Fichier
    {
        return $this->fichier;
    }

    public function setFichier(?Fichier $fichier): static
    {
        $this->fichier = $fichier;
        return $this;
    }

    public function getCitoyen(): ?Utilisateur
    {
        return $this->citoyen;
    }

    public function setCitoyen(?Utilisateur $citoyen): static
    {
        $this->citoyen = $citoyen;
        return $this;
    }

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): static
    {
        $this->quartier = $quartier;
        return $this;
    }
```

---

## **8-19. Relations restantes (plus simples)**

Je vous fournis le pattern, à adapter pour chaque entité :

### **Pattern général :**
```php
// AVANT (colonne INT)
#[ORM\Column(name: 'nom_table_id', ...)]
private ?int $nomTableId = null;

// APRÈS (relation)
#[ORM\ManyToOne(targetEntity: NomTable::class)]
#[ORM\JoinColumn(name: 'nom_table_id', nullable: false)]
private ?NomTable $nomTable = null;
```

### **Liste des relations restantes :**

**HistoriqueSignalement** :
- `signalement_id` → `Signalement` (ManyToOne)
- `modifie_par` → `Utilisateur` (ManyToOne, nullable)

**CommentairesSignalement** :
- `signalement_id` → `Signalement` (ManyToOne)
- `auteur_id` → `Utilisateur` (ManyToOne)

**Sondage** :
- `ville_id` → `Ville` (ManyToOne)
- `quartier_id` → `Quartier` (ManyToOne, nullable)
- `categorie_id` → `CategoriesCitoyen` (ManyToOne, nullable)
- `cree_par` → `Utilisateur` (ManyToOne)

**QuestionsSondage** :
- `sondage_id` → `Sondage` (ManyToOne)

**ReponsesSondage** :
- `question_id` → `QuestionsSondage` (ManyToOne)

**VotesSondage** :
- `sondage_id` → `Sondage` (ManyToOne)
- `citoyen_id` → `Utilisateur` (ManyToOne)
- `question_id` → `QuestionsSondage` (ManyToOne)
- `reponse_id` → `ReponsesSondage` (ManyToOne)

**Evenement** :
- `thematique_id` → `ThematiquesEvenement` (ManyToOne)
- `ville_id` → `Ville` (ManyToOne)
- `cree_par` → `Utilisateur` (ManyToOne)

**EvenementsUtilisateur** :
- `utilisateur_id` → `Utilisateur` (ManyToOne)
- `evenement_id` → `Evenement` (ManyToOne)

**Actualite** :
- `ville_id` → `Ville` (ManyToOne)
- `auteur_id` → `Utilisateur` (ManyToOne, nullable)

**ReseauSocial** :
- `ville_id` → `Ville` (ManyToOne)

**Notification** :
- `destinataire_id` → `Utilisateur` (ManyToOne)

**LogAction** :
- `utilisateur_id` → `Utilisateur` (ManyToOne, nullable)

---

## ✅ **Vérification après modification**

Après avoir ajouté toutes les relations :

```bash
# Vérifier que Doctrine reconnaît les relations
docker-compose exec symfony php bin/console doctrine:mapping:info

# Valider le schéma
docker-compose exec symfony php bin/console doctrine:schema:validate
```

**Résultat attendu :**
```
[Mapping]  OK - The mapping files are correct.
[Database] OK - The database schema is in sync with the mapping files.
```

---

## ⚡ **Alternative : Utiliser mon script automatique**

Si vous voulez tout automatiser, je peux créer un **script PHP complet** qui modifie toutes les entités automatiquement.

**Voulez-vous que je le crée ?**
