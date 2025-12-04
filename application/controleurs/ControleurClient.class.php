<?php
/**
 * ⚠️ CRITIQUE : Charger les dépendances AVANT d'utiliser les classes
 * Ces includes garantissent que ModelePDO, GestionClient et Database sont disponibles
 */
require_once __DIR__ . '/../modeles/ModelePDO.class.php';
require_once __DIR__ . '/../modeles/GestionClient.class.php';
require_once __DIR__ . '/../../config/database.php';

class ControleurClient {
    
    public function afficherConnexion() {
        require chemin(Chemins::VUES . 'front/connexion.php');
    }

    public function afficherInscription() {
        require chemin(Chemins::VUES . 'front/register.php');
    }

    public function traiterInscription() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                error_log("=== ControleurClient::traiterInscription() - DÉBUT ===");
                
                // Activer l'affichage des erreurs
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);

                $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $mdp = filter_input(INPUT_POST, 'mdp');
                
                // Récupérer les composants de la date
                $jour = filter_input(INPUT_POST, 'jour', FILTER_SANITIZE_NUMBER_INT);
                $mois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_NUMBER_INT);
                $annee = filter_input(INPUT_POST, 'annee', FILTER_SANITIZE_NUMBER_INT);
                
                // Debug des données reçues
                error_log("Données reçues - Nom: $nom, Prénom: $prenom, Email: $email");
                error_log("Date - Jour: $jour, Mois: $mois, Année: $annee");
                
                // Formater la date de naissance
                $date_naissance = $annee . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT) . '-' . str_pad($jour, 2, '0', STR_PAD_LEFT);
                error_log("Date formatée: $date_naissance");

                // Validation des données
                if ($nom && $prenom && $email && $mdp && $jour && $mois && $annee) {
                    error_log("Validation OK - Vérification email existant...");
                    
                    // ⚠️ CRITIQUE : Utiliser UNIQUEMENT GestionClient (qui utilise ModelePDO::getPDO())
                    // Aucune connexion directe dans le contrôleur
                    $client_existant = GestionClient::getClientParEmail($email);
                    if ($client_existant) {
                        error_log("Email déjà existant: $email");
                        header('Location: index.php?controleur=Client&action=afficherInscription&display=minimal&error=email_existe');
                        return;
                    }

                    error_log("Email disponible - Création du client via GestionClient::creerClient()");
                    
                    // ⚠️ CRITIQUE : Utiliser UNIQUEMENT GestionClient::creerClient()
                    // Cette méthode utilise ModelePDO::getPDO() pour la connexion Aiven
                    if (GestionClient::creerClient($nom, $prenom, $email, $mdp, $date_naissance)) {
                        error_log("Client créé avec succès - Redirection vers connexion");
                        header('Location: index.php?controleur=Client&action=afficherConnexion&display=minimal&inscription=success');
                        return;
                    } else {
                        error_log("ERREUR: GestionClient::creerClient() a retourné false");
                        throw new Exception("Erreur lors de la création du client dans la base de données");
                    }
                } else {
                    error_log("Données manquantes - Nom: $nom, Prénom: $prenom, Email: $email, Jour: $jour, Mois: $mois, Année: $annee");
                    header('Location: index.php?controleur=Client&action=afficherInscription&display=minimal&error=champs');
                    return;
                }
            } catch (PDOException $e) {
                error_log("ERREUR PDO dans traiterInscription(): " . $e->getMessage());
                error_log("Code: " . $e->getCode());
                error_log("Trace: " . $e->getTraceAsString());
                // Afficher l'erreur pour le débogage
                die("Erreur de base de données : " . $e->getMessage());
            } catch (Exception $e) {
                error_log("ERREUR dans traiterInscription(): " . $e->getMessage());
                error_log("Trace: " . $e->getTraceAsString());
                // Afficher l'erreur pour le débogage
                die("Erreur : " . $e->getMessage());
            }
        }
    }

    public function traiterConnexion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $mdp = filter_input(INPUT_POST, 'mdp');
                $remember = filter_input(INPUT_POST, 'remember', FILTER_VALIDATE_BOOLEAN);
                
                error_log("Tentative de connexion - Email: " . $email);
                
                // Vérifier si le client existe et si les identifiants sont corrects
                $client = GestionClient::verifierConnexion($email, $mdp);
                
                if ($client) {
                    // Démarrer la session si ce n'est pas déjà fait
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    error_log("Données client récupérées : " . print_r($client, true));
                    
                    // Stocker toutes les informations du client en session
                    $_SESSION['client_data'] = [
                        'id' => $client->id,
                        'nom' => $client->nom,
                        'prenom' => $client->prenom,
                        'email' => $client->email,
                        'tel' => $client->tel,
                        'rue' => $client->rue,
                        'codePostal' => $client->codePostal,
                        'ville' => $client->ville
                    ];
                    
                    // Garder aussi l'ID séparément pour la compatibilité
                    $_SESSION['client_id'] = $client->id;
                    $_SESSION['connecte'] = true;

                    // Charger le panier depuis la base pour ce client
                    require_once chemin(Chemins::MODELES . 'GestionPanier.class.php');
                    GestionPanier::fusionnerPanierSessionEtBase($client->id);

                    error_log("Données stockées en session : " . print_r($_SESSION, true));

                    // Si l'utilisateur a coché "Mémoriser mon ID"
                    if ($remember) {
                        // Créer un cookie sécurisé qui expire dans 30 jours
                        setcookie('remembered_email', $email, time() + (30 * 24 * 60 * 60), '/', null, true, true);
                        setcookie('remembered_password', base64_encode($mdp), time() + (30 * 24 * 60 * 60), '/', null, true, true);
                    } else {
                        // Si la case n'est pas cochée, on supprime les cookies existants
                        setcookie('remembered_email', '', time() - 3600, '/');
                        setcookie('remembered_password', '', time() - 3600, '/');
                    }
                    
                    error_log("Connexion réussie - Redirection vers l'accueil");
                    
                    // Rediriger vers la page d'accueil
                    header('Location: index.php');
                    return;
                } else {
                    error_log("Échec de la connexion - Identifiants incorrects");
                    header('Location: index.php?controleur=Client&action=afficherConnexion&display=minimal&error=connexion');
                    return;
                }
            } catch (Exception $e) {
                error_log("Erreur lors de la connexion : " . $e->getMessage());
                header('Location: index.php?controleur=Client&action=afficherConnexion&display=minimal&error=systeme');
                return;
            }
        }
    }

    public function deconnexion() {
        // Détruire la session du client
        session_destroy();
        header('Location: index.php');
        exit();
    }

    public function afficherProfil() {
        // S'assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        error_log("Tentative d'affichage du profil");
        error_log("Session ID: " . session_id());
        error_log("Client ID en session: " . (isset($_SESSION['client_id']) ? $_SESSION['client_id'] : 'non défini'));

        // Vérifier si le client est connecté
        if (!isset($_SESSION['client_id'])) {
            error_log("Client non connecté - Redirection vers la page de connexion");
            header('Location: index.php?controleur=Client&action=afficherConnexion&display=minimal');
            return;
        }

        // Récupérer les informations du client
        $client = GestionClient::getClientById($_SESSION['client_id']);
        
        if (!$client) {
            error_log("Client non trouvé dans la base de données avec l'ID: " . $_SESSION['client_id']);
            // Si le client n'existe pas, on le déconnecte
            session_destroy();
            header('Location: index.php?controleur=Client&action=afficherConnexion&display=minimal&error=client_inexistant');
            return;
        }

        // Appel de la fonction stockée pour récupérer le nombre de commandes du client
        require_once chemin(Chemins::MODELES . 'ModelePDO.class.php');
        $pdo = ModelePDO::getPDO();
        $stmt = $pdo->prepare("SELECT _selectNbCommandesByClient(:idClient) AS nbCommandes");
        $stmt->bindValue(':idClient', $client->id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nbCommandes = $result ? $result['nbCommandes'] : 0;
        $stmt->closeCursor();

        error_log("Client trouvé, affichage du profil pour : " . $client->nom . " " . $client->prenom);
        
        // Inclure uniquement la vue du profil
        require chemin(Chemins::VUES . 'front/profil/index.php');
    }

    public function afficherModificationProfil() {
        if (!isset($_SESSION['client_id'])) {
            header('Location: index.php?controleur=Client&action=afficherConnexion&display=minimal');
            return;
        }

        // Récupérer les informations du client
        $client = GestionClient::getClientById($_SESSION['client_id']);
        
        if (!$client) {
            session_destroy();
            header('Location: index.php?controleur=Client&action=afficherConnexion&display=minimal&error=client_inexistant');
            return;
        }

        // Inclure la vue de modification
        require chemin(Chemins::VUES . 'front/profil/modifier.php');
    }

    public function modifierProfil() {
        if (!isset($_SESSION['client_id'])) {
            header('Location: index.php?controleur=Client&action=afficherConnexion&display=minimal');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $date_naissance = filter_input(INPUT_POST, 'date_naissance');
            $rue = filter_input(INPUT_POST, 'rue', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $codePostal = filter_input(INPUT_POST, 'codePostal', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $ville = filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $nouveau_mdp = filter_input(INPUT_POST, 'nouveau_mdp');
            $confirmer_mdp = filter_input(INPUT_POST, 'confirmer_mdp');

            // Vérification des champs obligatoires
            if ($nom && $prenom && $date_naissance && $rue && $codePostal && $ville && $tel && $email) {
                try {
                    // Si un nouveau mot de passe est fourni
                    if ($nouveau_mdp) {
                        if ($nouveau_mdp !== $confirmer_mdp) {
                            header('Location: index.php?controleur=Client&action=afficherModificationProfil&error=mdp_different');
                            return;
                        }
                        // Mettre à jour avec le nouveau mot de passe
                        GestionClient::modifierClient(
                            $_SESSION['client_id'],
                            $nom,
                            $prenom,
                            $email,
                            $nouveau_mdp,
                            $date_naissance,
                            $rue,
                            $codePostal,
                            $ville,
                            $tel
                        );
                    } else {
                        // Mettre à jour sans le mot de passe
                        GestionClient::modifierClientSansMdp(
                            $_SESSION['client_id'],
                            $nom,
                            $prenom,
                            $email,
                            $date_naissance,
                            $rue,
                            $codePostal,
                            $ville,
                            $tel
                        );
                    }
                    
                    // Rediriger vers la page de profil avec un message de succès
                    header('Location: index.php?controleur=Client&action=afficherProfil&success=modification');
                    return;
                } catch (Exception $e) {
                    header('Location: index.php?controleur=Client&action=afficherModificationProfil&error=modification');
                    return;
                }
            } else {
                header('Location: index.php?controleur=Client&action=afficherModificationProfil&error=champs_obligatoires');
                return;
            }
        }
    }

    public function afficherCommandes() {
        if (!isset($_SESSION['client_id'])) {
            header('Location: index.php?controleur=Client&action=afficherConnexion&display=minimal');
            return;
        }
        require_once chemin(Chemins::MODELES . 'GestionBoutique.class.php');
        $commandes = GestionBoutique::getCommandesByClientId($_SESSION['client_id']);
        require chemin(Chemins::VUES . 'front/commandes/index.php');
    }
} 