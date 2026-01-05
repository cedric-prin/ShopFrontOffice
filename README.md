# 🛒 Prin Boutique — E-Commerce PHP MVC

**Prin Boutique** est une application e-commerce complète développée en **PHP natif** avec une architecture **MVC moderne**, une base de données **MySQL** entièrement structurée et un environnement **100% Docker Ready**.

## Projet professionnel démontrant :

- ✅ Architecture MVC avancée
- ✅ Validation centralisée
- ✅ Couche Services (logique métier claire)
- ✅ Gestion d'exceptions
- ✅ Sécurité côté serveur
- ✅ Base de données complète (procédures, triggers, fonctions)
- ✅ CI/CD (GitHub Actions)
- ✅ Tests PHPUnit

## 🎯 Fonctionnalités

### 👤 Côté Client

- ✅ Création de compte / Connexion
- ✅ Catalogue produits par catégories
- ✅ Panier dynamique (session + BDD)
- ✅ Processus de commande complet :
  - adresse → livraison → paiement → confirmation
- ✅ Historique des commandes
- ✅ Gestion du profil

### 🛠️ Côté Admin

- ✅ Dashboard
- ✅ CRUD Catégories
- ✅ CRUD Produits
- ✅ Gestion commandes, utilisateurs, fournisseurs
- ✅ Statistiques produits
- ✅ Réapprovisionnement automatique

### 🗄️ Base de Données

- ✅ Relations complètes
- ✅ Triggers (gestion automatique du stock)
- ✅ Procédures stockées
- ✅ Fonctions SQL (calculs, totaux)
- ✅ Migrations versionnées
- ✅ Scripts d'initialisation automatiques via Docker

## 🏗️ Architecture Technique

### Stack

- **PHP 8.2** (natif, sans framework)
- **MySQL 8.0**
- **Apache 2.4** + mod_rewrite
- **Docker & Docker Compose**
- **Composer** (autoload PSR-4)
- **Bootstrap / HTML / CSS / JS**

### Organisation du Code

```
application/
│── controleurs/
│── modeles/
│── services/
│── validation/
│── vues/
public/
config/
docker/
tests/
vendor/
```

## 🚀 Installation

### 🔧 Prérequis

- Docker Desktop
- Git

### 🐳 Lancement (Recommandé)

```bash
git clone https://github.com/cedric-prin/ShopFrontOffice.git
cd ShopFrontOffice
docker-compose up --build
```

🔗 **Accès à l'application :**
👉 http://localhost:8080

🗄️ **Base MySQL initialisée automatiquement** (tables + triggers + procédures).

### Configuration par défaut

- **Base** : `prin_boutique`
- **MySQL (host)** : `localhost:3307`
- **Utilisateur** : `cedric`
- **Mot de passe** : `cedric`

### Comptes de test

#### 👤 Admin
- **Identifiant** : `Chef`
- **Mot de passe** : `prin34`

#### 👤 Client
- Créer un compte via l'interface

## 🌐 Déploiement sur Render

### 🚀 Déploiement avec Aiven MySQL

L'application est configurée pour être déployée sur **Render** avec une base de données **Aiven MySQL**.

### 📋 Configuration requise sur Render

Pour que l'application fonctionne sur Render, vous devez définir les variables d'environnement suivantes dans le dashboard Render :

1. Allez sur https://dashboard.render.com
2. Sélectionnez votre service web
3. Allez dans l'onglet **Environment**
4. Cliquez sur **Add Environment Variable** pour chaque variable

#### ✅ Variables d'environnement complètes

| Key | Value | Description |
|-----|-------|-------------|
| `DB_HOST` | `mysql-shopfront-shopfrontoffice.b.aivencloud.com` | Host Aiven |
| `DB_PORT` | `22674` | Port Aiven |
| `DB_DATABASE` | `defaultdb` | Nom de la base de données |
| `DB_USERNAME` | `avnadmin` | Utilisateur Aiven |
| `DB_PASSWORD` | `[Votre mot de passe Aiven]` | ⚠️ Mot de passe Aiven (voir votre dashboard Aiven) |
| `DB_SSL_MODE` | `required` | ⚠️ **EN MINUSCULE** (pas REQUIRED) |
| `DB_SSL_CA` | *(laisser vide)* | Optionnel |

### ⚠️ Points critiques

1. **DB_SSL_MODE doit être en minuscule** : `required` (pas `REQUIRED` ou `Required`)
2. **DB_PASSWORD** : Récupérez-le depuis votre dashboard Aiven
3. **DB_SSL_CA** : Laisser vide (optionnel)

### 🔍 Vérification

Après avoir défini les variables :
1. Cliquez sur **Save Changes**
2. Render redéploiera automatiquement votre service
3. Vérifiez les logs Render pour confirmer que la connexion fonctionne
4. Testez l'inscription client

### 🗄️ Base de données Aiven

La base de données utilise **Aiven MySQL** avec :
- ✅ Connexion SSL sécurisée
- ✅ Configuration via variables d'environnement
- ✅ Support des migrations SQL
- ✅ Triggers et procédures stockées

Le fichier `config/database.php` utilise `getenv()` pour lire les variables d'environnement Render, garantissant que Render utilise toujours les variables définies dans le dashboard.

## 📸 Captures d'écran

### 🏠 Interface Client

#### Page d'accueil
![Accueil](docs/assets/screenshots/accueil.png)

#### Catalogue par catégorie
![Catégorie Disque Dur](docs/assets/screenshots/categorie_disque_dur.png)

#### Panier
![Panier](docs/assets/screenshots/panier.png)

![Panier Vide](docs/assets/screenshots/panier_vide.png)

#### Processus de commande

<div align="center">
  <h4>Étape 1 : Données personnelles</h4>
  <img src="docs/assets/screenshots/commande_donnees.png" width="45%">
  
  <h4>Étape 2 : Livraison et point relais</h4>
  <img src="docs/assets/screenshots/commande_livraison.png" width="45%">
  <img src="docs/assets/screenshots/point_relais.png" width="45%">
  
  <h4>Étape 3 : Paiement</h4>
  <img src="docs/assets/screenshots/commande_paiement.png" width="45%">
  
  <h4>Étape 4 : Récapitulatif</h4>
  <img src="docs/assets/screenshots/recap_commande.png" width="45%">
</div>

#### Historique des commandes
![Mes Commandes](docs/assets/screenshots/mes_commandes.png)

### 🛠️ Interface Administration

#### Dashboard Admin
![Dashboard Admin](docs/assets/screenshots/admin_accueil.png)

#### Gestion des catégories
![Gestion Catégories 1](docs/assets/screenshots/admin_categorie1.png)

![Gestion Catégories 2](docs/assets/screenshots/admin_categorie2.png)

## 🔄 Routing

### Moderne (recommandé)
- `/produits/afficher`
- `/client/connexion`
- `/admin/index`

### Classique (compatibilité)
- `?controleur=Produits&action=afficher`

## 🧪 Tests & CI/CD

### Tests PHPUnit

Structure :
```
tests/
│── Unit/
│── Feature/
```

### GitHub Actions

Pipeline continu :
- ✅ Lint PHP
- ✅ Tests PHPUnit
- ✅ Vérification structure/autoload

## 🎯 Objectifs du Projet

Ce projet met en avant :

- ✔ Une architecture MVC propre et structurée
- ✔ Un développement PHP modulaire et maintenable
- ✔ Une base de données cohérente et extensible
- ✔ L'utilisation de triggers, procédures et fonctions SQL
- ✔ Une gestion des sessions et une sécurité renforcée
- ✔ Une dockerisation complète pour un déploiement simple
- ✔ Une organisation du code de niveau professionnel (enterprise-grade)
- ✔ Une intégration continue CI/CD et des tests automatisés
- ✔ Une documentation claire et détaillée

## 📄 Licence

Projet sous licence propriétaire.

Toute reproduction, distribution ou modification est interdite sans autorisation.

## 📧 Contact

**prin.cedric.34@gmail.com**

---

❤️ **Développé par Cédric Prin en PHP natif**
