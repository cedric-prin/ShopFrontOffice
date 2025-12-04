<?php

/**
 * Classe de configuration de la base de données
 * 
 * Priorité de configuration :
 * 1. Variables d'environnement (pour Docker/Render/Aiven)
 * 2. Valeurs par défaut de la classe
 */
class Database {
    
    /**
     * Charge les variables d'environnement depuis le fichier .env si disponible
     */
    private static function loadEnv() {
        $envFile = dirname(__DIR__) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue; // Ignorer les commentaires
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    if (!isset($_ENV[$key])) {
                        $_ENV[$key] = $value;
                    }
                }
            }
        }
    }
    
    /**
     * Récupère une variable d'environnement ou retourne une valeur par défaut
     * Priorité : getenv() (pour Render) > $_ENV > .env file > default
     */
    private static function getEnv($key, $default) {
        // D'abord vérifier getenv() (utilisé par Render et autres plateformes cloud)
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        // Ensuite vérifier $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        // Enfin charger depuis .env si disponible
        self::loadEnv();
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        return $default;
    }
    
    // Configuration Aiven (par défaut)
    // ⚠️ NE PAS METTRE LE MOT DE PASSE ICI - Utiliser les variables d'environnement
    const HOSTNAME_DEFAULT = "mysql-shopfront-shopfrontoffice.b.aivencloud.com";  // Host Aiven
    const DATABASE_DEFAULT = "defaultdb";  // Database name Aiven
    const USERNAME_DEFAULT = "avnadmin";  // User Aiven
    const PASSWORD_DEFAULT = "";  // ⚠️ DOIT être défini via DB_PASSWORD dans les variables d'environnement
    const PORT_DEFAULT = 22674;  // Port Aiven
    const SSL_MODE_DEFAULT = "REQUIRED";  // SSL mode Aiven
    
    // Configuration pour Docker (fallback - uniquement si variables d'environnement non définies)
    // ⚠️ Par défaut, on utilise AIVEN. Docker doit être configuré via variables d'environnement.
    const HOSTNAME_DOCKER = "db";  // Nom du service dans docker-compose.yaml (fallback uniquement)
    const DATABASE_DOCKER = "prin_boutique";  // Fallback uniquement
    const USERNAME_DOCKER = "root";  // Fallback uniquement
    const PASSWORD_DOCKER = "";  // Fallback uniquement
    const PORT_DOCKER = 3306;  // Fallback uniquement
    
    /**
     * Hostname de la base de données
     * Priorité : DB_HOST (env) > HOSTNAME_DEFAULT > HOSTNAME_DOCKER
     */
    public static function getHostname() {
        return self::getEnv('DB_HOST', 
            self::getEnv('AIVEN_HOST', self::HOSTNAME_DEFAULT));
    }
    
    /**
     * Nom de la base de données
     */
    public static function getDatabase() {
        return self::getEnv('DB_DATABASE', 
            self::getEnv('AIVEN_DATABASE', self::DATABASE_DEFAULT));
    }
    
    /**
     * Nom d'utilisateur
     */
    public static function getUsername() {
        return self::getEnv('DB_USERNAME', 
            self::getEnv('AIVEN_USER', self::USERNAME_DEFAULT));
    }
    
    /**
     * Mot de passe
     */
    public static function getPassword() {
        return self::getEnv('DB_PASSWORD', 
            self::getEnv('AIVEN_PASSWORD', self::PASSWORD_DEFAULT));
    }
    
    /**
     * Port de connexion
     */
    public static function getPort() {
        $port = self::getEnv('DB_PORT', 
            self::getEnv('AIVEN_PORT', self::PORT_DEFAULT));
        return (int)$port;
    }
    
    /**
     * Mode SSL (REQUIRED pour Aiven)
     */
    public static function getSslMode() {
        return self::getEnv('DB_SSL_MODE', 
            self::getEnv('AIVEN_SSL_MODE', self::SSL_MODE_DEFAULT));
    }
    
    /**
     * Chemin vers le certificat CA (optionnel)
     */
    public static function getSslCa() {
        return self::getEnv('DB_SSL_CA', 
            self::getEnv('AIVEN_SSL_CA', ''));
    }
    
    // Constantes pour compatibilité avec l'ancien code
    // Utilisez les méthodes get*() ci-dessus pour les nouvelles implémentations
    const HOSTNAME = "mysql-shopfront-shopfrontoffice.b.aivencloud.com";
    const DATABASE = "defaultdb";
    const USERNAME = "avnadmin";
    const PASSWORD = ""; // ⚠️ DOIT être défini via DB_PASSWORD dans les variables d'environnement
    const PORT = 22674;
}

// Alias pour compatibilité
class_alias('Database', 'MySqlConfig');

?>

