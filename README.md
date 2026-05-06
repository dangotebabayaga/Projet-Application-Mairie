# Projet UniCity - Application Mairie

Application full-stack de gestion municipale (Angular + Symfony + PostgreSQL).

## Installation rapide pour les développeurs

### 1. Cloner le projet
```bash
git clone [votre-repo]
cd Projet-Application-Mairie
```

### 2. Lancer l'environnement Docker
```bash
docker-compose up -d
```

**Note:** La base de données est initialisée automatiquement au premier démarrage avec le fichier `docs/BD_amelioree_CORRIGEE.sql`.

### 3. Installer les dépendances Symfony
```bash
docker-compose exec symfony composer install
```

### 4. Vérifier l'installation
```bash
docker-compose exec symfony php bin/console doctrine:mapping:info
# Résultat attendu: 19 entités mappées
```

## Réinitialiser la base de données

Si vous devez réinitialiser complètement la base:
```bash
docker-compose down -v  # Supprime les volumes (données)
docker-compose up -d    # Recrée et réinitialise automatiquement
```

## Accès aux services

- **Backend API**: http://localhost:8000
- **Frontend Angular**: http://localhost:4200
- **PostgreSQL**: localhost:5432
  - Database: `unicity_db`
  - User: `unicity_user`
  - Password: `unicity_pass`

## Endpoints de test

- `GET /test/db` - Test connexion base de données
- `GET /test/relations` - Test relations Doctrine
- `GET /test/query` - Test requêtes avec jointures

## Technologies

- **Backend**: Symfony 7.2.9 + PHP 8.3 + Doctrine ORM
- **Frontend**: Angular 19
- **Base de données**: PostgreSQL 15
- **Conteneurisation**: Docker

## Structure

```
backend-symfony/app/
├── src/
│   ├── Entity/          # 19 entités Doctrine
│   ├── Repository/      # 19 repositories
│   └── Controller/      # Controllers API
docs/
└── BD_amelioree_CORRIGEE.sql  # Structure + données initiales
```

## Licence

Projet académique - MIAGE 2026
