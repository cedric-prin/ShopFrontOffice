<?php
/**
 * Configuration de la base de données Aiven pour Render
 * 
 * ⚠️ CRITIQUE : Ce fichier utilise UNIQUEMENT getenv() pour lire les variables d'environnement Render
 * Aucune valeur par défaut locale (localhost, 3306, root) n'est utilisée
 * 
 * Variables d'environnement requises sur Render :
 * - DB_HOST : mysql-shopfront-shopfrontoffice.b.aivencloud.com
 * - DB_PORT : 22674
 * - DB_DATABASE : defaultdb
 * - DB_USERNAME : avnadmin
 * - DB_PASSWORD : [votre mot de passe Aiven]
 * - DB_SSL_MODE : required (en minuscule)
 * - DB_SSL_CA : (optionnel, laisser vide)
 */

class Database {
    
    /**
     * Hostname de la base de données Aiven
     * @return string
     */
    public static function getHostname() {
        return getenv('DB_HOST');
    }
    
    /**
     * Nom de la base de données
     * @return string
     */
    public static function getDatabase() {
        return getenv('DB_DATABASE');
    }
    
    /**
     * Nom d'utilisateur
     * @return string
     */
    public static function getUsername() {
        return getenv('DB_USERNAME');
    }
    
    /**
     * Mot de passe
     * @return string
     */
    public static function getPassword() {
        return getenv('DB_PASSWORD');
    }
    
    /**
     * Port de connexion
     * @return int|string
     */
    public static function getPort() {
        return getenv('DB_PORT');
    }
    
    /**
     * Mode SSL (required par défaut pour Aiven)
     * @return string
     */
    public static function getSslMode() {
        return getenv('DB_SSL_MODE') ?: 'required';
    }
    
    /**
     * Chemin vers le certificat CA (optionnel)
     * @return string|null
     */
    public static function getSslCa() {
        $ca = getenv('DB_SSL_CA');
        return $ca ?: null;
    }
}

// Alias pour compatibilité avec l'ancien code
if (!class_exists('MySqlConfig')) {
    class_alias('Database', 'MySqlConfig');
}

?>
