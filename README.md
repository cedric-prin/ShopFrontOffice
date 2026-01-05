# 🛒 Prin Boutique — Front Office E-Commerce PHP

**Prin Boutique** est une application e-commerce complète développée en **PHP natif** avec une architecture **MVC**. Ce projet a été réalisé dans le cadre de ma formation **BTS SIO SLAM** pour mettre en pratique les concepts de développement web backend, de gestion de base de données et de déploiement.

L'objectif était de créer une application fonctionnelle, bien structurée et déployable, en appliquant les bonnes pratiques de développement PHP moderne.

---

## 📸 Aperçu visuel

### Interface client

La page d'accueil présente le catalogue des produits organisés par catégories, avec une navigation intuitive et un design responsive.

![Page d'accueil](docs/assets/screenshots/accueil.png)

Le processus de commande est divisé en plusieurs étapes claires : saisie des données personnelles, choix de la livraison et du point relais, puis finalisation du paiement.

![Processus de commande - Données personnelles](docs/assets/screenshots/commande_donnees.png)
![Processus de commande - Livraison](docs/assets/screenshots/commande_livraison.png)

### Interface administration

Le dashboard admin permet de gérer les catégories, les produits, les commandes et d'accéder aux statistiques de vente.

![Dashboard Admin](docs/assets/screenshots/admin_accueil.png)

---

## 🎯 Fonctionnalités principales

### Côté client
- **Authentification** : Inscription et connexion sécurisées
- **Catalogue produits** : Affichage par catégories avec recherche
- **Panier dynamique** : Gestion en session et persistance en base de données
- **Processus de commande** : Workflow complet en plusieurs étapes (adresse → livraison → paiement → confirmation)
- **Historique** : Consultation des commandes passées
- **Profil utilisateur** : Modification des informations personnelles

### Côté administration
- **Dashboard** : Vue d'ensemble des statistiques
- **Gestion des catégories** : CRUD complet (Créer, Lire, Modifier, Supprimer)
- **Gestion des produits** : Ajout, modification, suppression avec gestion du stock
- **Gestion des commandes** : Suivi et traitement des commandes clients
- **Statistiques** : Analyse des ventes et des produits les plus vendus
- **Réapprovisionnement** : Gestion automatique des stocks

---

## 🛠️ Stack technique

- **PHP 8.2** : Langage backend natif (sans framework)
- **PDO** : Accès à la base de données avec requêtes préparées
- **MySQL 8.0** : Base de données relationnelle
- **Docker & Docker Compose** : Containerisation pour le développement
- **Render** : Plateforme de déploiement cloud
- **Composer** : Gestionnaire de dépendances PHP (autoload PSR-4)
- **Variables d'environnement** : Configuration sécurisée via `.env`

---

## 🏗️ Architecture du projet

Le projet suit une architecture **MVC (Modèle-Vue-Contrôleur)** pour séparer clairement les responsabilités :

```
prin_boutique/
│
├── application/          # Code source de l'application
│   ├── controleurs/     # Logique de routage et orchestration
│   ├── modeles/         # Accès aux données (PDO, requêtes SQL)
│   ├── services/        # Logique métier réutilisable
│   ├── validation/      # Validation des données
│   ├── vues/            # Templates d'affichage (front + admin)
│   └── bootstrap.php    # Point d'entrée, routing, chargement des classes
│
├── public/              # Point d'entrée web (DocumentRoot)
│   ├── index.php        # Redirection vers bootstrap.php
│   ├── css/             # Feuilles de style
│   ├── js/              # Scripts JavaScript
│   └── images/          # Images statiques
│
├── config/              # Configuration de l'application
│   ├── database.php     # Classe Database (variables d'environnement)
│   ├── app.php          # Configuration générale
│   └── paths.php        # Chemins de l'application
│
├── database/            # Scripts SQL
│   └── migrations/      # Migrations versionnées
│
├── docker/              # Configuration Docker
│   ├── apache.conf      # Configuration Apache
│   └── sql/             # Scripts d'initialisation BDD
│
├── tests/               # Tests PHPUnit
│   ├── Unit/            # Tests unitaires
│   └── Feature/         # Tests d'intégration
│
├── vendor/              # Dépendances Composer (ignoré par Git)
├── .env                 # Variables d'environnement (ignoré par Git)
├── ENV.example          # Exemple de configuration
├── Dockerfile           # Image Docker PHP/Apache
└── docker-compose.yaml  # Orchestration Docker
```

### Le dossier `public/` : point d'entrée web

Le dossier `public/` est configuré comme **DocumentRoot** (racine web). Seuls les fichiers dans ce dossier sont accessibles directement via le navigateur. Cette pratique de sécurité empêche l'accès direct aux fichiers sensibles (config, modèles, contrôleurs) qui restent dans `application/`.

Le fichier `public/index.php` redirige toutes les requêtes vers `application/bootstrap.php` qui gère le routing MVC.

---

## ⚙️ Gestion de la configuration

### Le fichier `.env`

Les informations sensibles (mots de passe, clés API, URLs) ne sont **jamais** stockées dans le code source. Elles sont définies dans un fichier `.env` à la racine du projet, qui est **ignoré par Git** (voir `.gitignore`).

### Exemple de configuration (`ENV.example`)

Le fichier `ENV.example` sert de modèle pour créer votre propre `.env` :

```env
# Base de données
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=prin_boutique
DB_USERNAME=root
DB_PASSWORD=

# Application
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8080
```

### Pourquoi cette pratique ?

- ✅ **Sécurité** : Les credentials ne sont pas versionnés dans Git
- ✅ **Flexibilité** : Configuration différente selon l'environnement (dev, production)
- ✅ **Collaboration** : Chaque développeur peut avoir sa propre configuration locale
- ✅ **Déploiement** : Sur Render, les variables sont définies dans le dashboard

---

## 🚀 Lancer le projet en local

### Prérequis

- **Docker Desktop** installé et démarré
- **Git** pour cloner le repository
- **Composer** (optionnel, pour les dépendances)

### Méthode 1 : Avec Docker (recommandé)

Cette méthode lance automatiquement PHP, Apache et MySQL dans des conteneurs isolés :

```bash
# Cloner le repository
git clone https://github.com/cedric-prin/ShopFrontOffice.git
cd ShopFrontOffice

# Créer le fichier .env à partir de l'exemple
cp ENV.example .env
# Éditer .env et configurer vos paramètres locaux

# Lancer les conteneurs
docker-compose up --build
```

L'application sera accessible sur **http://localhost:8080**

La base de données MySQL sera automatiquement initialisée avec les tables, triggers et procédures stockées.

### Méthode 2 : Sans Docker (WAMP/XAMPP)

Si vous préférez utiliser WAMP ou XAMPP :

1. Copiez le projet dans le dossier `www/` de WAMP
2. Créez une base de données `prin_boutique` dans phpMyAdmin
3. Importez le script SQL : `database/migrations/001_init_database.sql`
4. Configurez votre `.env` avec les paramètres de votre serveur local
5. Accédez à l'application via `http://localhost/prin_boutique/public/`

### Commandes utiles (avec Docker)

```bash
# Voir les logs
docker-compose logs -f

# Arrêter les conteneurs
docker-compose down

# Réinitialiser la base de données
docker-compose down -v
docker-compose up -d
```

---

## 🗄️ Connexion à la base de données

### Fonctionnement avec PDO

Le projet utilise **PDO (PHP Data Objects)** pour communiquer avec MySQL. PDO offre plusieurs avantages :

- ✅ **Requêtes préparées** : Protection contre les injections SQL
- ✅ **Gestion d'erreurs** : Exceptions claires en cas de problème
- ✅ **Portabilité** : Code compatible avec différentes bases de données

### Utilisation des variables d'environnement

La connexion est centralisée dans la classe `Database` (`config/database.php`) qui lit les variables d'environnement :

```php
// config/database.php
class Database {
    public static function getHostname() {
        return getenv('DB_HOST');
    }
    public static function getDatabase() {
        return getenv('DB_DATABASE');
    }
    // ... autres méthodes
}
```

Tous les modèles héritent de `ModelePDO` qui utilise cette configuration :

```php
// application/modeles/ModelePDO.class.php
$host = Database::getHostname();
$db = Database::getDatabase();
$user = Database::getUsername();
$pass = Database::getPassword();

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$pdo = new PDO($dsn, $user, $pass, $options);
```

Cette approche garantit que :
- ✅ Les credentials ne sont jamais dans le code source
- ✅ La configuration change selon l'environnement (local vs production)
- ✅ La connexion est centralisée et facile à maintenir

---

## ☁️ Déploiement sur Render

### Principe général

**Render** est une plateforme cloud qui permet de déployer des applications web facilement. Le projet est configuré pour être déployé sur Render avec une base de données **Aiven MySQL** (service MySQL managé).

### Configuration des variables d'environnement

Sur Render, les variables d'environnement sont définies dans le **dashboard** (onglet Environment) :

| Variable | Description | Exemple |
|----------|-------------|---------|
| `DB_HOST` | Hostname Aiven | `mysql-shopfront-xxx.b.aivencloud.com` |
| `DB_PORT` | Port MySQL | `22674` |
| `DB_DATABASE` | Nom de la base | `defaultdb` |
| `DB_USERNAME` | Utilisateur Aiven | `avnadmin` |
| `DB_PASSWORD` | Mot de passe Aiven | `[votre mot de passe]` |
| `DB_SSL_MODE` | Mode SSL (obligatoire) | `required` |

⚠️ **Important** : `DB_SSL_MODE` doit être en **minuscule** (`required`, pas `REQUIRED`).

### Séparation application / base de données

- **Application** : Déployée sur Render (service web)
- **Base de données** : Hébergée sur Aiven (service MySQL managé)

Cette séparation permet :
- ✅ Une meilleure scalabilité
- ✅ Une sécurité renforcée (base de données isolée)
- ✅ Des sauvegardes automatiques (gérées par Aiven)

### Fichier `render.yaml`

Le fichier `render.yaml` à la racine définit la configuration de déploiement :

```yaml
services:
  - type: web
    name: prin-boutique
    runtime: docker
    dockerfilePath: ./Dockerfile
    envVars:
      - key: DB_HOST
        value: mysql-shopfront-xxx.b.aivencloud.com
      # ... autres variables
```

---

## 🔒 Sécurité & bonnes pratiques

### Variables d'environnement

✅ **À faire** : Stocker les credentials dans `.env`  
❌ **À éviter** : Hardcoder les mots de passe dans le code

Le fichier `.env` est dans `.gitignore`, donc jamais commité sur Git.

### Dossier `public/`

✅ **À faire** : Mettre tous les fichiers accessibles publiquement dans `public/`  
❌ **À éviter** : Exposer les fichiers de configuration ou de code source

Seul `public/index.php` est accessible via le navigateur. Le reste du code (`application/`, `config/`) est protégé.

### Fichiers ignorés par Git

Le `.gitignore` exclut :
- `.env` : Variables d'environnement sensibles
- `vendor/` : Dépendances Composer (à réinstaller avec `composer install`)
- `logs/` : Fichiers de logs
- `*.pem` : Certificats SSL
- `storage/` : Fichiers générés (factures PDF, etc.)

### Requêtes préparées

Toutes les requêtes SQL utilisent des **requêtes préparées** PDO pour éviter les injections SQL :

```php
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
```

---

## 🎓 Axes d'amélioration

En tant que développeur junior, je suis conscient que ce projet peut être amélioré. Voici les axes sur lesquels je souhaite progresser :

### Architecture MVC
- ✅ **Actuel** : Architecture MVC fonctionnelle avec séparation des responsabilités
- 🔄 **Amélioration** : Implémenter un système de routing plus avancé (comme Symfony Router)
- 🔄 **Amélioration** : Ajouter une couche de middleware pour la gestion des erreurs

### Authentification
- ✅ **Actuel** : Système de connexion basique avec sessions
- 🔄 **Amélioration** : Implémenter JWT (JSON Web Tokens) pour une API REST
- 🔄 **Amélioration** : Ajouter la réinitialisation de mot de passe par email
- 🔄 **Amélioration** : Système de rôles et permissions plus granulaire

### Tests
- ✅ **Actuel** : Structure de tests PHPUnit en place
- 🔄 **Amélioration** : Augmenter la couverture de code (actuellement limitée)
- 🔄 **Amélioration** : Ajouter des tests d'intégration pour les workflows complets

### Sécurité
- ✅ **Actuel** : Requêtes préparées, variables d'environnement, protection CSRF basique
- 🔄 **Amélioration** : Implémenter une validation plus stricte des entrées utilisateur
- 🔄 **Amélioration** : Ajouter un système de rate limiting pour les API
- 🔄 **Amélioration** : Audit de sécurité complet (OWASP Top 10)

### API REST
- 🔄 **Futur** : Développer une API REST complète pour une application mobile
- 🔄 **Futur** : Documentation API avec Swagger/OpenAPI
- 🔄 **Futur** : Versioning de l'API

Ces améliorations sont des objectifs d'apprentissage pour progresser vers un niveau plus avancé.

---

## 👤 Auteur

**Cédric Prin**  
Étudiant en **BTS SIO SLAM** (Services Informatiques aux Organisations - Solutions Logicielles et Applications Métiers)

📧 **Contact** : prin.cedric.34@gmail.com

---

## 📄 Licence

Ce projet est sous licence propriétaire. Toute reproduction, distribution ou modification est interdite sans autorisation écrite préalable.

---

*Développé avec PHP natif, en suivant les bonnes pratiques de développement web moderne.*