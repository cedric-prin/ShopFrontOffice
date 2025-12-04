<?php
require_once 'ModelePDO.class.php';

class GestionClient extends ModelePDO {
    
    protected static $pdoCnxBase;
    protected static $pdoStResults;
    protected static $requete;
    protected static $resultat;

    /**
     * Se connecter à la base de données
     * Utilise la connexion centralisée ModelePDO avec configuration Aiven
     */
    public static function seConnecter() {
        // Utiliser la méthode parente qui gère la connexion Aiven
        parent::seConnecter();
        // Récupérer la connexion PDO partagée
        self::$pdoCnxBase = parent::getPDO();
    }

    /**
     * Crée un nouveau client dans la base de données.
     * 
     * @param string $nom Le nom du client
     * @param string $prenom Le prénom du client
     * @param string $email L'email du client
     * @param string $mot_de_passe Le mot de passe du client
     * @param string $date_naissance La date de naissance du client
     * @return bool True si la création a réussi, false sinon
     */
    public static function creerClient($nom, $prenom, $email, $mot_de_passe, $date_naissance) {
        try {
            error_log("Début de la création du client dans la base de données", 0);
            self::seConnecter();
            
            error_log("Connexion à la base de données établie", 0);
            self::$requete = "INSERT INTO client (nom, prenom, email, mdp, date_naissance) 
                             VALUES (:nom, :prenom, :email, :mdp, :date_naissance)";
            
            error_log("Préparation de la requête SQL", 0);
            self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
            
            error_log("Liaison des paramètres", 0);
            self::$pdoStResults->bindValue(':nom', $nom, PDO::PARAM_STR);
            self::$pdoStResults->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            self::$pdoStResults->bindValue(':email', $email, PDO::PARAM_STR);
            self::$pdoStResults->bindValue(':mdp', password_hash($mot_de_passe, PASSWORD_DEFAULT), PDO::PARAM_STR);
            self::$pdoStResults->bindValue(':date_naissance', $date_naissance, PDO::PARAM_STR);
            
            error_log("Exécution de la requête", 0);
            $resultat = self::$pdoStResults->execute();
            
            if ($resultat) {
                error_log("Client créé avec succès dans la base de données", 0);
            } else {
                error_log("Échec de la création du client dans la base de données", 0);
                error_log("Erreur PDO : " . implode(" ", self::$pdoStResults->errorInfo()), 0);
            }
            
            self::$pdoStResults->closeCursor();
            return $resultat;
        } catch (PDOException $e) {
            error_log("Exception PDO lors de la création du client : " . $e->getMessage(), 0);
            throw $e;
        }
    }

    /**
     * Récupère un client par son ID.
     * 
     * @param int $id L'ID du client
     * @return object Le client correspondant à l'ID
     */
    public static function getClientById($id) {
        try {
            self::seConnecter();
            self::$requete = "SELECT * FROM client WHERE id = :id";
            self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
            self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
            self::$pdoStResults->execute();
            self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
            self::$pdoStResults->closeCursor();
            return self::$resultat;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du client : " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère un client par son email.
     * 
     * @param string $email L'email du client
     * @return object Le client correspondant à l'email
     */
    public static function getClientParEmail($email) {
        self::seConnecter();
        self::$requete = "SELECT * FROM client WHERE email = :email";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':email', $email, PDO::PARAM_STR);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère tous les clients.
     * 
     * @return array La liste de tous les clients
     */
    public static function getTousLesClients() {
        self::seConnecter();
        self::$requete = "SELECT * FROM client ORDER BY nom, prenom";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Retourne la liste des clients (alias pour compatibilité).
     * @return array Tableau d'objets client
     */
    public static function getLesClients() {
        return self::getTousLesClients();
    }

    /**
     * Retourne un client par son email (alias pour compatibilité).
     * @param string $email L'email du client à récupérer
     * @return object|null Objet représentant un client ou null si non trouvé
     */
    public static function getClientByEmail($email) {
        return self::getClientParEmail($email);
    }

    /**
     * Modifie les informations d'un client, y compris le mot de passe.
     * 
     * @param int $id L'ID du client
     * @param string $nom Le nouveau nom
     * @param string $prenom Le nouveau prénom
     * @param string $email Le nouvel email
     * @param string $mot_de_passe Le nouveau mot de passe
     * @param string $date_naissance La nouvelle date de naissance
     * @param string $rue La nouvelle rue
     * @param string $codePostal Le nouveau code postal
     * @param string $ville La nouvelle ville
     * @param string $tel Le nouveau téléphone
     * @return bool True si la modification a réussi, false sinon
     */
    public static function modifierClient($id, $nom, $prenom, $email, $mot_de_passe, $date_naissance, $rue, $codePostal, $ville, $tel) {
        self::seConnecter();
        self::$requete = "UPDATE client 
                         SET nom = :nom, prenom = :prenom, email = :email, 
                             mdp = :mdp, date_naissance = :date_naissance,
                             rue = :rue, codePostal = :codePostal, 
                             ville = :ville, tel = :tel 
                         WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':nom', $nom, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':email', $email, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':mdp', password_hash($mot_de_passe, PASSWORD_DEFAULT), PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':date_naissance', $date_naissance, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':rue', $rue, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':codePostal', $codePostal, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':ville', $ville, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':tel', $tel, PDO::PARAM_STR);
        $resultat = self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Modifie les informations d'un client sans changer le mot de passe.
     * 
     * @param int $id L'ID du client
     * @param string $nom Le nouveau nom
     * @param string $prenom Le nouveau prénom
     * @param string $email Le nouvel email
     * @param string $date_naissance La nouvelle date de naissance
     * @param string $rue La nouvelle rue
     * @param string $codePostal Le nouveau code postal
     * @param string $ville La nouvelle ville
     * @param string $tel Le nouveau téléphone
     * @return bool True si la modification a réussi, false sinon
     */
    public static function modifierClientSansMdp($id, $nom, $prenom, $email, $date_naissance, $rue, $codePostal, $ville, $tel) {
        self::seConnecter();
        self::$requete = "UPDATE client 
                         SET nom = :nom, prenom = :prenom, email = :email, 
                             date_naissance = :date_naissance,
                             rue = :rue, codePostal = :codePostal, 
                             ville = :ville, tel = :tel 
                         WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':nom', $nom, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':email', $email, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':date_naissance', $date_naissance, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':rue', $rue, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':codePostal', $codePostal, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':ville', $ville, PDO::PARAM_STR);
        self::$pdoStResults->bindValue(':tel', $tel, PDO::PARAM_STR);
        $resultat = self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Supprime un client.
     * 
     * @param int $id L'ID du client à supprimer
     * @return bool True si la suppression a réussi, false sinon
     */
    public static function supprimerClient($id) {
        self::seConnecter();
        self::$requete = "DELETE FROM client WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        $resultat = self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Vérifie les identifiants de connexion d'un client.
     * 
     * @param string $email L'email du client
     * @param string $mot_de_passe Le mot de passe du client
     * @return object|false Le client si la connexion est réussie, false sinon
     */
    public static function verifierConnexion($email, $mot_de_passe) {
        try {
            error_log("Tentative de connexion pour l'email : " . $email);
            $client = self::getClientParEmail($email);
            if ($client && password_verify($mot_de_passe, $client->mdp)) {
                error_log("Connexion réussie pour l'utilisateur : " . $client->prenom);
                return $client;
            }
            error_log("Échec de la connexion pour l'email : " . $email);
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la vérification de connexion : " . $e->getMessage());
            throw $e;
        }
    }
}
