<?php
// Démarrer le buffer de sortie AVANT session_start pour éviter les erreurs de headers
ob_start();

// Forcer l'encodage UTF-8 pour tout le script
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Pour éviter erreurs SESSIONS

// Définir le chemin racine du projet
define('ROOT_PATH', dirname(__DIR__) . '/');

// Calculer le chemin de base pour les assets (CSS, JS, images)
// Fonctionne avec localhost/prin_boutique/public/ ou localhost:8000 ou Render
$script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$script_dir = dirname($script_name);

// Détecter si on est sur Render
// Sur Render, le DocumentRoot pointe vers /var/www/html/public
// Donc SCRIPT_NAME = /index.php et dirname = /
// En localhost avec WAMP, SCRIPT_NAME = /prin_boutique/public/index.php et dirname = /prin_boutique/public
$is_render = (strpos($script_name, '/public/') === false && ($script_dir === '/' || $script_dir === '\\' || $script_dir === '.'));

// Si on est dans un sous-dossier (ex: /prin_boutique/public/), garder le chemin complet
// Si on est à la racine (ex: /), utiliser le chemin vide (Render)
$base_asset_path = rtrim($script_dir, '/');

// Si le script est à la racine (/index.php), le chemin de base est vide
if ($script_dir === '/' || $script_dir === '\\' || $script_dir === '.') {
    $base_asset_path = '';
}

define('BASE_ASSET_PATH', $base_asset_path);

/**
 * Fonction helper pour obtenir les chemins CSS/JS/Images
 * Utilisation : asset_path('css/style.css') ou asset_path('images/logo.jpg')
 */
if (!function_exists('asset_path')) {
    function asset_path($path) {
        $base = BASE_ASSET_PATH;
        $asset = ltrim($path, '/');
        
        // Si BASE_ASSET_PATH est vide (Render ou racine), retourner directement le chemin
        if (empty($base)) {
            return '/' . $asset;
        }
        
        // Sinon, concaténer avec un slash
        // S'assurer qu'il n'y a pas de double slash
        $base = rtrim($base, '/');
        return $base . '/' . $asset;
    }
}

// Autoload Composer (PSR-4)
// Charger Composer autoload si disponible (pour les dépendances comme TCPDF)
if (file_exists(ROOT_PATH . 'vendor/autoload.php')) {
    require_once ROOT_PATH . 'vendor/autoload.php';
}

require_once ROOT_PATH . 'config/paths.php';
require_once chemin(Paths::CONFIG . 'database.php');
require_once chemin(Paths::CONFIG . 'app.php');

// Alias pour compatibilité avec l'ancien code
if (!class_exists('Chemins')) {
    class_alias('Paths', 'Chemins');
}
if (!class_exists('MySqlConfig')) {
    class_alias('Database', 'MySqlConfig');
}
if (!class_exists('VariablesGlobales')) {
    class_alias('App', 'VariablesGlobales');
}

// Charger les exceptions
require_once chemin(Paths::MODELES . '../exceptions/ProduitNotFoundException.php');
require_once chemin(Paths::MODELES . '../exceptions/PanierVideException.php');
require_once chemin(Paths::MODELES . '../exceptions/CommandeNotFoundException.php');
require_once chemin(Paths::MODELES . '../exceptions/ValidationException.php');

/**
 * Gestionnaire d'exceptions global
 */
function exceptionHandler($exception) {
    // Log de l'erreur
    error_log("Exception: " . $exception->getMessage() . " dans " . $exception->getFile() . " ligne " . $exception->getLine());
    
    // Afficher un message utilisateur si la méthode existe
    if (method_exists($exception, 'getMessageUtilisateur')) {
        $message = $exception->getMessageUtilisateur();
    } else {
        $message = "Une erreur est survenue. Veuillez réessayer plus tard.";
    }
    
    // Rediriger vers une page d'erreur ou afficher via une vue
    if (!headers_sent()) {
        header("Location: /erreur?message=" . urlencode($message));
    } else {
        // Si les headers sont déjà envoyés, afficher via une vue d'erreur
        if (defined('ROOT_PATH') && file_exists(chemin(Paths::VUES_FRONT . 'erreur.php'))) {
            $errorMessage = $message;
            require chemin(Paths::VUES_FRONT . 'erreur.php');
        }
    }
}

// Enregistrer le gestionnaire d'exceptions
set_exception_handler('exceptionHandler');
// Classes de gestion (GestionBoutique inclut les autres pour compatibilité)
require_once chemin(Paths::MODELES . 'GestionBoutique.class.php');
require_once chemin(Paths::MODELES . 'GestionClient.class.php');
require_once chemin(Paths::MODELES . 'GestionCategorie.class.php');
require_once chemin(Paths::MODELES . 'GestionProduit.class.php');
require_once chemin(Paths::MODELES . 'GestionFournisseur.class.php');
require_once chemin(Paths::MODELES . 'GestionCommande.class.php');
require_once chemin(Paths::MODELES . 'GestionLigneDeCommande.class.php');
require_once chemin(Paths::MODELES . 'GestionUtilisateur.class.php');
require_once chemin(Paths::MODELES . 'GestionLivraison.class.php');
require_once chemin(Paths::MODELES . 'GestionPanier.class.php');
require_once chemin(Paths::MODELES . 'GestionAdmin.class.php');

require_once chemin(Paths::CONTROLEURS . 'ControleurProduits.class.php');
require_once chemin(Paths::CONTROLEURS . 'ControleurClient.class.php');
require_once chemin(Paths::CONTROLEURS . 'ControleurAdmin.class.php');
require_once chemin(Paths::CONTROLEURS . 'ControleurPanier.class.php');

// Récupération du contrôleur et de l'action depuis l'URL
// Supporte deux formats :
// 1. Moderne : ?url=produits/afficher (via .htaccess)
// 2. Ancien : ?controleur=Produits&action=afficher (compatibilité)

$controleur = 'Produits';
$action = 'afficher';

// Si on a le paramètre 'url' (routing moderne via .htaccess)
if (isset($_GET['url']) && !empty($_GET['url'])) {
    $url = trim($_GET['url'], '/');
    $parts = explode('/', $url);
    $controleur = !empty($parts[0]) ? ucfirst($parts[0]) : 'Produits';
    $action = !empty($parts[1]) ? $parts[1] : 'afficher';
} else {
    // Ancien système (compatibilité)
    $controleur = isset($_GET['controleur']) ? $_GET['controleur'] : 'Produits';
    $action = isset($_GET['action']) ? $_GET['action'] : 'afficher';
}

// Actions qui doivent toujours être en mode minimal (sans header/footer)
$actionsMinimales = [
    'connexion',
    'afficherConnexion',
    'afficherInscription',
    'inscription',
    'traiterConnexion',
    'traiterInscription'
];

// Déterminer si on doit afficher le header/footer
$displayMinimal = false;

// Vérifier le paramètre display explicite
if (isset($_REQUEST['display']) && $_REQUEST['display'] === 'minimal') {
    $displayMinimal = true;
}

// Vérifier si l'action nécessite automatiquement le mode minimal
if (!$displayMinimal && $controleur === 'Client' && in_array($action, $actionsMinimales)) {
    $displayMinimal = true;
}

// Vérifier si c'est une page admin (sauf connexion admin et index admin)
// Les pages admin doivent être en mode minimal sauf si c'est la connexion
if (!$displayMinimal && $controleur === 'Admin') {
    // Si l'admin n'est pas connecté et qu'on affiche l'index, c'est la page de connexion → mode minimal
    if ($action === 'afficherIndex' && !isset($_SESSION['login_admin'])) {
        $displayMinimal = true;
    }
    // Toutes les autres actions admin sont en mode minimal
    elseif ($action !== 'afficherIndex' && $action !== 'verifierConnexion') {
        $displayMinimal = true;
    }
}

$displayHeaderFooter = !$displayMinimal;

if ($displayHeaderFooter) {
    require chemin(Paths::VUES_LAYOUT . 'entete.php');
}

// error_log debug désactivé en production
// error_log('>>> Requête : ' . $_SERVER['REQUEST_METHOD'] . ' - controleur=' . (isset($_GET['controleur']) ? $_GET['controleur'] : '') . ' - action=' . (isset($_GET['action']) ? $_GET['action'] : ''));

// Création des instances de contrôleurs
$controleurProduits = new ControleurProduits();
$controleurClient = new ControleurClient();
$controleurAdmin = new ControleurAdmin();
$controleurPanier = new ControleurPanier();

// Routage des requêtes
switch ($controleur) {
    case 'Panier':
        switch ($action) {
            case 'afficherAdresse':
                $controleurPanier->afficherAdresse();
                break;
            case 'validerAdresse':
                $controleurPanier->validerAdresse();
                break;
            case 'afficherLivraison':
                $controleurPanier->afficherLivraison();
                break;
            case 'validerLivraison':
                $controleurPanier->validerLivraison();
                break;
            case 'afficherPaiement':
                $controleurPanier->afficherPaiement();
                break;
            case 'validerPaiement':
                $controleurPanier->validerPaiement();
                break;
            case 'processPayPalPayment':
                $controleurPanier->processPayPalPayment();
                break;
            case 'confirmation':
                $controleurPanier->confirmation();
                break;
            default:
                $controleurPanier->afficherAdresse();
        }
        break;

    case 'Produits':
        switch ($action) {
            case 'afficher':
                $controleurProduits->afficher();
                break;
            case 'afficherPanier':
                $controleurProduits->afficherPanier();
                break;
            case 'AjouterPanier':
                $controleurProduits->AjouterPanier();
                break;
            case 'retirerPanier':
                $controleurProduits->retirerPanier();
                break;
            case 'viderPanier':
                $controleurProduits->viderPanier();
                break;
            case 'MettreAJourPanier':
                $controleurProduits->MettreAJourPanier();
                break;
            case 'commander':
                $controleurProduits->commander();
                break;
            case 'confirmationCommande':
                $controleurProduits->confirmationCommande();
                break;
            case 'afficherCheckoutAdresse':
                $controleurProduits->afficherCheckoutAdresse();
                break;
            default:
                // Action par défaut
                $controleurProduits->afficher();
        }
        break;

    case 'Client':
        switch ($action) {
            case 'connexion':
            case 'afficherConnexion':
                $controleurClient->afficherConnexion();
                break;
            case 'inscription':
            case 'afficherInscription':
                $controleurClient->afficherInscription();
                break;
            case 'traiterConnexion':
                $controleurClient->traiterConnexion();
                break;
            case 'traiterInscription':
                $controleurClient->traiterInscription();
                break;
            case 'deconnexion':
                $controleurClient->deconnexion();
                break;
            case 'afficherProfil':
                $controleurClient->afficherProfil();
                break;
            case 'afficherModificationProfil':
                $controleurClient->afficherModificationProfil();
                break;
            case 'modifierProfil':
                $controleurClient->modifierProfil();
                break;
            case 'afficherCommandes':
                $controleurClient->afficherCommandes();
                break;
            default:
                // Action par défaut
                $controleurClient->afficherConnexion();
        }
        break;

    case 'Admin':
        switch ($action) {
            case 'afficherIndex':
                $controleurAdmin->afficherIndex();
                break;
            case 'afficherIndexAdmin':
                $controleurAdmin->afficherIndexAdmin();
                break;
            case 'verifierConnexion':
                $controleurAdmin->verifierConnexion();
                break;
            case 'seDeconnecter':
                $controleurAdmin->seDeconnecter();
                break;
            case 'VoirCategorie':
            case 'voirCategorie':
                $controleurAdmin->VoirCategorie();
                break;
            case 'voirStatsProduits':
                $controleurAdmin->voirStatsProduits();
                break;
            case 'reapprovisionnerProduit':
                $controleurAdmin->reapprovisionnerProduit();
                break;
            default:
                // Action par défaut
                $controleurAdmin->afficherIndex();
        }
        break;

    case 'Categories':
        require_once chemin(Paths::CONTROLEURS . 'ControleurCategories.class.php');
        $controleurCategories = new ControleurCategories();
        
        switch ($action) {
            case 'ajouter':
                $controleurCategories->ajouter();
                break;
            case 'supprimer':
                $controleurCategories->supprimer();
                break;
            case 'modifier':
                $controleurCategories->modifier();
                break;
            default:
                // Action par défaut
                $controleurCategories->afficher();
        }
        break;

    default:
        // Contrôleur par défaut
        $controleurProduits->afficher();
}

// Résumé du panier et pied de page

if ($displayHeaderFooter) {
    // require chemin(Chemins::VUES_PERMANENTES . 'v_resume_panier.inc.php');
    require chemin(Paths::VUES_LAYOUT . 'pied.php');
}
?>

