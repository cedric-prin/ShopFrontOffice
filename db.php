<?php
/**
 * Fichier de connexion MySQLi unifié avec SSL pour Aiven
 * 
 * Ce fichier centralise toutes les connexions MySQLi du projet.
 * Utilise la configuration Database pour récupérer les paramètres Aiven.
 * 
 * ⚠️ IMPORTANT : Le mot de passe doit être défini via DB_PASSWORD dans les variables d'environnement
 * 
 * @return mysqli|false Connexion MySQLi ou false en cas d'erreur
 */
require_once __DIR__ . '/config/database.php';

// Récupérer les paramètres de connexion Aiven
$host = Database::getHostname();
$port = Database::getPort();
$dbname = Database::getDatabase();
$username = Database::getUsername();
$password = Database::getPassword();
$ssl_ca = Database::getSslCa();

// Vérifier que les paramètres sont définis
if (empty($host) || empty($dbname) || empty($username) || empty($password)) {
    error_log('ERREUR: Paramètres de connexion Aiven manquants');
    error_log('Host: ' . ($host ?: 'VIDE'));
    error_log('Database: ' . ($dbname ?: 'VIDE'));
    error_log('Username: ' . ($username ?: 'VIDE'));
    error_log('Password: ' . ($password ? 'DEFINI' : 'VIDE'));
    die("Erreur de configuration : Paramètres de connexion Aiven manquants. Vérifiez les variables d'environnement.");
}

// Initialiser la connexion MySQLi
$conn = mysqli_init();

// Configuration SSL pour Aiven (REQUIRED)
if (!empty($ssl_ca)) {
    // Si un chemin de certificat CA est fourni, l'utiliser
    $ssl_ca_path = $ssl_ca;
    // Si le chemin est relatif, le convertir en absolu depuis la racine du projet
    if (!file_exists($ssl_ca_path) && defined('ROOT_PATH')) {
        $ssl_ca_path = ROOT_PATH . $ssl_ca_path;
    }
    // Si toujours relatif, essayer depuis __DIR__
    if (!file_exists($ssl_ca_path)) {
        $ssl_ca_path = __DIR__ . '/' . ltrim($ssl_ca, '/');
    }
    
    if (file_exists($ssl_ca_path)) {
        // Utiliser le certificat CA fourni
        mysqli_ssl_set($conn, NULL, NULL, $ssl_ca_path, NULL, NULL);
    } else {
        // Pas de certificat, mais SSL requis quand même (Aiven accepte sans certificat avec vérification désactivée)
        mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    }
} else {
    // Pas de certificat fourni, mais SSL requis pour Aiven
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
}

// Connexion avec SSL obligatoire pour Aiven
$connected = mysqli_real_connect(
    $conn,
    $host,
    $username,
    $password,
    $dbname,
    $port,
    NULL,
    MYSQLI_CLIENT_SSL
);

// Vérifier la connexion
if (!$connected || mysqli_connect_errno()) {
    $error_msg = "Erreur de connexion Aiven : " . mysqli_connect_error() . " (Code: " . mysqli_connect_errno() . ")";
    error_log($error_msg);
    error_log("Host: $host:$port");
    error_log("Database: $dbname");
    error_log("User: $username");
    die($error_msg);
}

// Définir le charset UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Retourner la connexion
return $conn;

?>
