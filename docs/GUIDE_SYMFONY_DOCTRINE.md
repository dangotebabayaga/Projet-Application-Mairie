# 📘 GUIDE : Synchronisation Symfony + Doctrine avec BD_UniCity

**Projet :** Application UniCity - Master 2 MIAGe 2026
**Date :** 2025-12-06
**Symfony :** 7.2.9
**Doctrine ORM :** 3.5.8
**PostgreSQL :** 15

---

## 🎯 **Situation actuelle**

✅ **Base de données `BD_UniCity` créée et peuplée** via SQL
✅ **19 tables** avec données de test
✅ **Connexion Doctrine** fonctionnelle
❌ **Entités PHP** : Pas encore créées
❌ **Migrations** : Pas encore générées

---

## 🔄 **Approche : SQL d'abord, Symfony ensuite**

Vous avez choisi l'**Option 3 : Combinaison SQL + Migrations Symfony**

### **Avantages de cette approche :**
1. ✅ **Démarrage rapide** : BDD opérationnelle immédiatement
2. ✅ **Contrôle total** : Vous voyez exactement la structure SQL
3. ✅ **Évolutivité** : Doctrine gérera les futures modifications
4. ✅ **Professionnel** : Migration versionnée pour le travail d'équipe

---

## 📋 **Étapes à suivre pour synchroniser Symfony**

### **Étape 1 : Créer les entités PHP manuellement** (Recommandé pour Symfony 7)

Avec Symfony 7, la génération automatique depuis la BDD n'est plus supportée nativement.
**Solution moderne** : Créer les entités manuellement avec `make:entity`

#### **🔧 Créer l'entité `Utilisateur` (exemple)**

```bash
cd Projet-Application-Mairie
docker-compose exec symfony php bin/console make:entity Utilisateur
```

**Symfony va vous demander :**
```
New property name (press <return> to stop adding fields):
> nom

Field type (enter ? to see all types) [string]:
> string

Field length [255]:
> 100

Can this field be null in the database (nullable) (yes/no) [no]:
> no
```

**Répétez pour chaque champ :** `prenom`, `email`, `motDePasseHash`, `role`, etc.

---

### **Étape 2 : Utiliser un générateur automatique (Alternative)**

Si vous voulez générer automatiquement toutes les entités, installez un package tiers :

```bash
docker-compose exec symfony composer require --dev doctrine/doctrine-fixtures-bundle
docker-compose exec symfony composer require --dev javiereguiluz/easyentity-bundle
```

Puis :
```bash
docker-compose exec symfony php bin/console make:entity --regenerate
```

---

### **Étape 3 : Créer la migration "initiale"** ⚠️ **ATTENTION**

Une fois vos entités créées, générez une migration :

```bash
docker-compose exec symfony php bin/console make:migration
```

**⚠️ PROBLÈME** : Cette migration va essayer de **recréer toutes les tables** qui existent déjà !

**Solution** : **Ne PAS exécuter cette migration**, ou la modifier pour qu'elle soit vide.

---

### **Étape 4 : Marquer la migration comme exécutée** (sans l'exécuter)

```bash
# Voir la liste des migrations
docker-compose exec symfony php bin/console doctrine:migrations:list

# Marquer MANUELLEMENT la migration comme exécutée (sans toucher à la BDD)
docker-compose exec symfony php bin/console doctrine:migrations:version --add --all
```

Cela dit à Symfony : "Ces migrations ont déjà été appliquées, ne les exécute plus jamais"

---

### **Étape 5 : Tester que tout fonctionne**

```bash
# Vérifier que Doctrine reconnait bien la BDD
docker-compose exec symfony php bin/console doctrine:schema:validate

# Résultat attendu :
# [OK] The mapping files are correct.
# [OK] The database schema is in sync with the mapping files.
```

---

## 🚀 **Workflow pour les FUTURES modifications**

Maintenant que Symfony est synchronisé, voici comment travailler :

### **Scénario 1 : Ajouter une nouvelle table**

**Étape 1** : Créer l'entité
```bash
docker-compose exec symfony php bin/console make:entity NomTable
```

**Étape 2** : Générer la migration
```bash
docker-compose exec symfony php bin/console make:migration
```

**Étape 3** : Vérifier le SQL généré
```bash
cat backend-symfony/app/migrations/VersionXXXXXX.php
```

**Étape 4** : Exécuter la migration
```bash
docker-compose exec symfony php bin/console doctrine:migrations:migrate
```

---

### **Scénario 2 : Ajouter une colonne à une table existante**

**Étape 1** : Modifier l'entité PHP
```bash
docker-compose exec symfony php bin/console make:entity Utilisateur
# Ajouter le nouveau champ
```

**Étape 2** : Générer la migration (comme ci-dessus)

---

### **Scénario 3 : Modifier directement en SQL (déconseillé mais possible)**

Si vous modifiez la BDD directement en SQL :

**Étape 1** : Exécuter votre SQL
```bash
docker-compose exec db psql -U user -d BD_UniCity -c "ALTER TABLE Utilisateur ADD COLUMN nouveau_champ VARCHAR(100);"
```

**Étape 2** : Synchroniser l'entité PHP manuellement
```bash
# Éditer src/Entity/Utilisateur.php
# Ajouter le champ
```

**Étape 3** : Créer une migration vide
```bash
docker-compose exec symfony php bin/console make:migration
# Supprimer le contenu auto-généré
# Marquer comme exécutée : --add
```

---

## 📊 **Liste des entités à créer**

Pour synchroniser complètement, créez ces entités dans cet ordre (dépendances) :

### **1. Entités de base (sans dépendances)**
- ✅ `Ville`
- ✅ `Quartier` (dépend de Ville)
- ✅ `CategorieCitoyen` (dépend de Ville)
- ✅ `ThematiqueEvenement` (dépend de Ville)

### **2. Entités Utilisateur**
- ✅ `Utilisateur` (dépend de Ville, Quartier, CategorieCitoyen)

### **3. Entités fonctionnelles**
- ✅ `Fichier` (dépend de Utilisateur)
- ✅ `Signalement` (dépend de Utilisateur, Quartier, Fichier)
- ✅ `HistoriqueSignalement` (dépend de Signalement, Utilisateur)
- ✅ `CommentaireSignalement` (dépend de Signalement, Utilisateur)
- ✅ `Sondage` (dépend de Ville, Quartier, CategorieCitoyen, Utilisateur)
- ✅ `QuestionSondage` (dépend de Sondage)
- ✅ `ReponseSondage` (dépend de QuestionSondage)
- ✅ `VoteSondage` (dépend de Sondage, Utilisateur, QuestionSondage, ReponseSondage)
- ✅ `Evenement` (dépend de ThematiqueEvenement, Ville, Utilisateur)
- ✅ `EvenementUtilisateur` (dépend de Utilisateur, Evenement)
- ✅ `Actualite` (dépend de Ville, Utilisateur)
- ✅ `ReseauSocial` (dépend de Ville)
- ✅ `Notification` (dépend de Utilisateur)
- ✅ `LogAction` (dépend de Utilisateur)

---

## 🛠️ **Script rapide pour créer toutes les entités**

Voici un script bash pour accélérer la création (à adapter selon vos besoins) :

```bash
#!/bin/bash

# Liste des entités à créer
ENTITIES=(
  "Ville"
  "Quartier"
  "CategorieCitoyen"
  "ThematiqueEvenement"
  "Utilisateur"
  "Fichier"
  "Signalement"
  "HistoriqueSignalement"
  "CommentaireSignalement"
  "Sondage"
  "QuestionSondage"
  "ReponseSondage"
  "VoteSondage"
  "Evenement"
  "EvenementUtilisateur"
  "Actualite"
  "ReseauSocial"
  "Notification"
  "LogAction"
)

for entity in "${ENTITIES[@]}"; do
  echo "Créez l'entité $entity manuellement avec make:entity"
  # docker-compose exec symfony php bin/console make:entity $entity
  # Symfony demandera les propriétés de manière interactive
done
```

---

## 💡 **Recommandations importantes**

### **1. Nommage des entités (Conventions Symfony)**

| Table BDD | Entité PHP | Nom de fichier |
|-----------|----------|----------------|
| `Utilisateur` | `Utilisateur` | `Utilisateur.php` |
| `signalements` | `Signalement` | `Signalement.php` |
| `votes_sondage` | `VoteSondage` | `VoteSondage.php` |

**Doctrine convertit automatiquement** :
- `CamelCase` → `snake_case` pour les tables
- `Utilisateur` → `Utilisateur`

### **2. Annotations vs Attributs PHP 8**

**Symfony 7 recommande les attributs PHP 8** :

```php
// ✅ MODERNE (PHP 8 Attributes)
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'Utilisateur')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
}

// ❌ ANCIEN (Annotations - deprecated)
/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @ORM\Table(name="Utilisateur")
 */
class Utilisateur{ }
```

### **3. Relations entre entités**

Exemple : `Utilisateur` ↔ `Ville`

```php
// Dans Utilisateur.php
#[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'Utilisateur')]
#[ORM\JoinColumn(nullable: false)]
private ?Ville $ville = null;

// Dans Ville.php
#[ORM\OneToMany(mappedBy: 'ville', targetEntity: Utilisateur::class)]
private Collection $Utilisateur;
```

---

## 🔍 **Vérifications de santé**

```bash
# 1. Vérifier que Doctrine voit bien la BDD
docker-compose exec symfony php bin/console dbal:run-sql "SELECT COUNT(*) FROM Utilisateur"

# 2. Lister les entités reconnues par Doctrine
docker-compose exec symfony php bin/console doctrine:mapping:info

# 3. Valider le schéma (après création des entités)
docker-compose exec symfony php bin/console doctrine:schema:validate

# 4. Voir les différences entre entités et BDD
docker-compose exec symfony php bin/console doctrine:schema:update --dump-sql
```

---

## 📝 **Exemple complet : Créer l'entité Utilisateur**

### **Fichier : `src/Entity/Utilisateur.php`**

```php
<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'Utilisateur')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'mot_de_passe_hash', length: 255)]
    private ?string $motDePasseHash = null;

    #[ORM\Column(length: 50)]
    private ?string $role = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $adresse = null;

    #[ORM\ManyToOne(targetEntity: Ville::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $ville = null;

    #[ORM\ManyToOne(targetEntity: Quartier::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Quartier $quartier = null;

    #[ORM\ManyToOne(targetEntity: CategorieCitoyen::class)]
    #[ORM\JoinColumn(name: 'categorie_id', nullable: true)]
    private ?CategorieCitoyen $categorie = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, name: 'date_naissance', nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(name: 'consentement_rgpd', options: ['default' => false])]
    private ?bool $consentementRgpd = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name: 'date_consentement_rgpd', nullable: true)]
    private ?\DateTimeInterface $dateConsentementRgpd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name: 'derniere_connexion', nullable: true)]
    private ?\DateTimeInterface $derniereConnexion = null;

    #[ORM\Column(name: 'compte_actif', options: ['default' => true])]
    private ?bool $compteActif = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name: 'date_creation')]
    private ?\DateTimeInterface $dateCreation = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->consentementRgpd = false;
        $this->compteActif = true;
    }

    // Getters et Setters...
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    // ... (autres getters/setters)
}
```

---

## 🎯 **Prochaines étapes recommandées**

1. **Court terme (cette semaine)**
   - [ ] Créer les 5 entités principales : `Ville`, `Utilisateur`, `Signalement`, `Sondage`, `Evenement`
   - [ ] Tester les requêtes Doctrine
   - [ ] Créer vos premiers contrôleurs API

2. **Moyen terme (semaine prochaine)**
   - [ ] Créer toutes les entités restantes
   - [ ] Implémenter l'authentification JWT
   - [ ] Créer les endpoints API REST

3. **Long terme**
   - [ ] Tests unitaires avec PHPUnit
   - [ ] Fixtures de données
   - [ ] Documentation API avec API Platform

---

## ❓ **FAQ**

### **Q : Dois-je créer TOUTES les entités maintenant ?**
**R :** Non ! Commencez par les principales (`Utilisateur`, `Signalement`, `Sondage`). Créez les autres au fur et à mesure des besoins.

### **Q : Que faire si je modifie la BDD en SQL directement ?**
**R :** Synchronisez manuellement l'entité PHP correspondante, puis créez une migration vide et marquez-la comme exécutée.

### **Q : Les migrations vont-elles écraser ma BDD actuelle ?**
**R :** Non, SI vous suivez l'étape 4 (marquer les migrations comme exécutées sans les jouer).

### **Q : Doctrine peut-il générer automatiquement depuis la BDD ?**
**R :** Plus depuis Symfony 6+. C'est une décision volontaire de Symfony pour encourager l'approche "code-first".

---

## 📚 **Ressources utiles**

- [Doctrine ORM Documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/)
- [Symfony Doctrine Guide](https://symfony.com/doc/current/doctrine.html)
- [Migrations Doctrine](https://symfony.com/doc/current/doctrine/migrations.html)
- [Make Entity Command](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html#doctrine-entities)

---

**Bon courage ! Votre base de données `BD_UniCity` est prête, il ne reste plus qu'à créer les entités PHP. 🚀**
