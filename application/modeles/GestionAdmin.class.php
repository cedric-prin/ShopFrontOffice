<?php

require_once 'ModelePDO.class.php';

class GestionAdmin {
    /**
     * Utilise la connexion PDO centralisée avec configuration Aiven
     */
    private static function getPDO() {
        ModelePDO::seConnecter();
        return ModelePDO::getPDO();
    }

    public static function verifierConnexionAdmin($login, $passe) {
        try {
            $pdo = self::getPDO();
            
            if ($pdo === null) {
                error_log('ERREUR: Connexion PDO non établie dans verifierConnexionAdmin()');
                return false;
            }
            
            $requete = "SELECT * FROM administrateur WHERE login = :login AND passe = :passe";
            $stmt = $pdo->prepare($requete);
            
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':passe', $passe);
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification admin : " . $e->getMessage());
            return false;
        }
    }

    public static function getAdminByLogin($login) {
        try {
            $pdo = self::getPDO();
            
            if ($pdo === null) {
                error_log('ERREUR: Connexion PDO non établie dans getAdminByLogin()');
                return null;
            }
            
            $requete = "SELECT * FROM administrateur WHERE login = :login";
            $stmt = $pdo->prepare($requete);
            
            $stmt->bindParam(':login', $login);
            
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération admin : " . $e->getMessage());
            return null;
        }
    }
}

?> 