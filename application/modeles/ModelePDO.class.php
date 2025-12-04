<?php
/**
 * ⚠️ CRITIQUE : Charger la configuration Database AVANT d'utiliser la classe
 * Ce fichier DOIT être chargé pour que Database::get*() fonctionne
 */
require_once __DIR__ . '/../../config/database.php';

class ModelePDO {

//Attributs utiles pour la connexion
    protected static $serveur = null;
    protected static $base = null;
    protected static $utilisateur = null;
    protected static $passe = null;
    protected static $port = null;
    protected static $ssl_mode = null;
    protected static $ssl_ca = null;
//Attributs utiles pour la manipulation PDO de la BD
    protected static $pdoCnxBase = null;
    protected static $pdoStResults = null;
    protected static $requete = "";
    protected static $resultat = null;

    /**
     * Initialise les paramètres de connexion depuis la classe Database
     * ⚠️ CRITIQUE : Cette méthode DOIT être appelée après que database.php soit chargé
     */
    private static function initConfig() {
        if (self::$serveur === null) {
            // Vérifier que la classe Database est disponible
            if (!class_exists('Database')) {
                error_log('ERREUR CRITIQUE: Classe Database non trouvée dans initConfig()');
                error_log('Vérifiez que config/database.php est bien chargé');
                throw new Exception('Classe Database non disponible - config/database.php non chargé');
            }
            
            // Logs de debug pour diagnostiquer les problèmes de configuration
            error_log("INITCONFIG: hostname=" . Database::getHostname());
            error_log("INITCONFIG: username=" . Database::getUsername());
            error_log("INITCONFIG: database=" . Database::getDatabase());
            error_log("INITCONFIG: port=" . Database::getPort());
            error_log("INITCONFIG: password=" . (Database::getPassword() ? 'OK' : 'VIDE'));
            error_log("INITCONFIG: ssl_mode=" . Database::getSslMode());
            
            // Utiliser les méthodes de Database qui supportent les variables d'environnement
            self::$serveur = Database::getHostname();
            self::$base = Database::getDatabase();
            self::$utilisateur = Database::getUsername();
            self::$passe = Database::getPassword();
            self::$port = Database::getPort();
            self::$ssl_mode = Database::getSslMode();
            self::$ssl_ca = Database::getSslCa();
            
            // Vérifier que tous les paramètres critiques sont définis
            if (empty(self::$serveur) || empty(self::$base) || empty(self::$utilisateur) || empty(self::$passe)) {
                error_log('ERREUR: Paramètres de connexion incomplets après initConfig()');
                error_log('Serveur: ' . (self::$serveur ?: 'VIDE'));
                error_log('Base: ' . (self::$base ?: 'VIDE'));
                error_log('User: ' . (self::$utilisateur ?: 'VIDE'));
                error_log('Password: ' . (self::$passe ? 'DEFINI' : 'VIDE'));
            }
        }
    }

    public static function seConnecter() {
        if (!isset(self::$pdoCnxBase)) { //S'il n'y a pas encore eu de connexion
            try {
                self::initConfig();
                
                // Vérifier que les paramètres sont bien définis
                if (empty(self::$serveur) || empty(self::$base) || empty(self::$utilisateur) || empty(self::$passe)) {
                    error_log('ERREUR: Paramètres de connexion manquants');
                    error_log('Serveur: ' . (self::$serveur ?: 'VIDE'));
                    error_log('Base: ' . (self::$base ?: 'VIDE'));
                    error_log('User: ' . (self::$utilisateur ?: 'VIDE'));
                    error_log('Password: ' . (self::$passe ? 'DEFINI' : 'VIDE'));
                    throw new Exception('Paramètres de connexion à la base de données manquants');
                }
                
                // Construction du DSN avec le port pour Aiven
                $dsn = 'mysql:host=' . self::$serveur . 
                       ';port=' . self::$port . 
                       ';dbname=' . self::$base . 
                       ';charset=utf8mb4';
                
                // Options PDO pour SSL (requis pour Aiven)
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'",
                ];
                
                // Configuration SSL pour Aiven
                // Accepter 'required' (minuscule) ou 'REQUIRED' (majuscule)
                $ssl_mode_lower = strtolower(self::$ssl_mode ?: 'required');
                if ($ssl_mode_lower === 'required') {
                    // Si un chemin de certificat CA est fourni, l'utiliser
                    // Sinon, Aiven fonctionne aussi sans certificat avec SSL_VERIFY_SERVER_CERT = false
                    if (!empty(self::$ssl_ca)) {
                        $ssl_ca_path = self::$ssl_ca;
                        // Si le chemin est relatif, le convertir en absolu depuis ROOT_PATH
                        if (!file_exists($ssl_ca_path) && defined('ROOT_PATH')) {
                            $ssl_ca_path = ROOT_PATH . $ssl_ca_path;
                        }
                        if (file_exists($ssl_ca_path)) {
                            $options[PDO::MYSQL_ATTR_SSL_CA] = $ssl_ca_path;
                        }
                    }
                    // Aiven utilise des certificats auto-signés, donc on désactive la vérification
                    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                }
                
                self::$pdoCnxBase = new PDO($dsn, self::$utilisateur, self::$passe, $options);
                // Forcer l'encodage UTF-8 pour corriger les problèmes d'accents
                self::$pdoCnxBase->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                self::$pdoCnxBase->exec("SET CHARACTER SET 'utf8mb4'");
                self::$pdoCnxBase->exec("SET character_set_connection = 'utf8mb4'");
                self::$pdoCnxBase->exec("SET character_set_results = 'utf8mb4'");
                self::$pdoCnxBase->exec("SET character_set_client = 'utf8mb4'");
            } catch (Exception $e) {
                // Logger l'erreur pour le débogage
                error_log('Erreur de connexion PDO: ' . $e->getMessage());
                error_log('DSN: ' . ($dsn ?? 'NON DÉFINI'));
                error_log('User: ' . (self::$utilisateur ?: 'VIDE'));
                error_log('Host: ' . (self::$serveur ?: 'VIDE') . ':' . (self::$port ?: 'VIDE'));
                error_log('Password: ' . (self::$passe ? 'DÉFINI' : 'VIDE'));
                
                // Ne pas afficher l'erreur en production, mais la logger
                if (defined('APP_DEBUG') && APP_DEBUG) {
                    echo 'Erreur de connexion : ' . $e->getMessage() . '<br />';
                    echo 'Code : ' . $e->getCode();
                } else {
                    // En production, rediriger vers une page d'erreur générique
                    if (!headers_sent()) {
                        header('Location: /erreur?message=Une+erreur+de+base+de+données+est+survenue.');
                        exit();
                    }
                }
                
                // Ne pas définir $pdoCnxBase pour éviter les erreurs "on null"
                self::$pdoCnxBase = null;
            }
        }
    }

    protected static function seDeconnecter() {
        self::$pdoCnxBase = null;
// Si on n'appelle pas la méthode, la déconnexion a lieu en fin de script
    }

    protected static function getLesTuples($table) {
        self::seConnecter();
        
        // Vérifier que la connexion est bien établie
        if (self::$pdoCnxBase === null) {
            error_log('ERREUR: Connexion PDO null dans getLesTuples()');
            return [];
        }
        
        self::$requete = "SELECT * FROM " . $table;
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    protected static function getPremierTupleByChamp($table, $nomChamp, $valeurChamp) {
        self::seConnecter();
        
        // Vérifier que la connexion est bien établie
        if (self::$pdoCnxBase === null) {
            error_log('ERREUR: Connexion PDO null dans getPremierTupleByChamp()');
            return null;
        }
        
        self::$requete = "SELECT * FROM " . $table . " WHERE " . $nomChamp . " = :valeurChamp";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':valeurChamp', $valeurChamp);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    // Méthode pour modifier un tuple dans une table
    protected static function modifierTuple($table, $champsValeurs, $conditions) {
        // $champsValeurs est un tableau associatif avec les champs à modifier
        // $conditions est une chaîne de conditions pour spécifier quel(s) enregistrement(s) à modifier
        // Construction de la partie SET de la requête SQL
        $setParts = [];
        foreach ($champsValeurs as $champ => $valeur) {
            $setParts[] = $champ . " = :" . $champ;
        }
        $setString = implode(", ", $setParts);

        // Construction de la requête SQL complète
        self::seConnecter();
        self::$requete = "UPDATE " . $table . " SET " . $setString . " WHERE " . $conditions;

        // Préparation de la requête
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);

        // Liaison des valeurs des champs
        foreach ($champsValeurs as $champ => $valeur) {
            self::$pdoStResults->bindValue(':' . $champ, $valeur);
        }

        // Exécution de la requête
        self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
    }

    protected static function SupprimerTupleByChamp($table, $nomChamp, $valeurChamp) {
        self::seConnecter();
        self::$requete = "DELETE FROM " . $table . " WHERE " . $nomChamp . " = :valeurChamp";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':valeurChamp', $valeurChamp);
        self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
    }

    protected static function select($champs, $tables, $conditions = null) {
        self::seConnecter();
        self::$requete = "SELECT " . $champs . " FROM " . $tables;
        if ($conditions != null)
            self::$requete .= " WHERE " . $conditions;
    }

    protected static function getNbProduits() {
        self::seConnecter();
        self::$requete = "SELECT Count(*) AS nbProduits FROM Produit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch();
        self::$pdoStResults->closeCursor();
        return self::$resultat->nbProduits;
    }

    protected static function getLesTuplesByTable($table) {
        self::seConnecter();
        if (self::$pdoCnxBase === null) {
            error_log('ERREUR: Connexion PDO non établie dans getLesTuplesByTable()');
            return [];
        }
        self::$requete = "SELECT * FROM $table";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll();
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    protected static function getLeTupleTableById($table, $id) {
        self::seConnecter();
        if (self::$pdoCnxBase === null) {
            error_log('ERREUR: Connexion PDO non établie dans getLeTupleTableById()');
            return null;
        }
        self::$requete = "SELECT * FROM $table WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch();
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Vérifie si l'utilisateur est un administrateur présent dans la base
     * @param type $login Login de l'utilisateur
     * @param type $passe Passe de l'utilisateur
     * @return type Booléen
     */
    protected static function isAdminOK($login, $passe) {
        self::seConnecter();
        
        // Vérifier que la connexion est bien établie
        if (self::$pdoCnxBase === null) {
            error_log('ERREUR: Connexion PDO null dans isAdminOK()');
            return false;
        }
        
        self::$requete = "SELECT * FROM utilisateur where login=:login and passe=:passe";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue('login', $login);
        self::$pdoStResults->bindValue('passe', $passe);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch();
        self::$pdoStResults->closeCursor();
        if ((self::$resultat != null) and (self::$resultat->isAdmin))
            return true;
        else
            return false;
    }
    
    /**
     * Récupère la connexion PDO Aiven
     * ⚠️ NE JAMAIS créer de connexion directe - TOUJOURS utiliser cette méthode
     * @return PDO|null La connexion PDO ou null si la connexion a échoué
     */
    public static function getPDO() {
        self::seConnecter();
        
        // Vérifier que la connexion est bien établie
        if (self::$pdoCnxBase === null) {
            error_log('ERREUR CRITIQUE: Connexion PDO null dans getPDO()');
            error_log('Vérifiez que DB_PASSWORD est défini dans les variables d\'environnement Render');
            error_log('Host: ' . (self::$serveur ?: 'NON DÉFINI'));
            error_log('Database: ' . (self::$base ?: 'NON DÉFINI'));
            error_log('User: ' . (self::$utilisateur ?: 'NON DÉFINI'));
            error_log('Password: ' . (self::$passe ? 'DÉFINI' : 'VIDE - ⚠️ PROBLÈME'));
        }
        
        return self::$pdoCnxBase;
    }
}
