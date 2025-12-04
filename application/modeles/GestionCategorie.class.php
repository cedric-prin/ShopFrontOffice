<?php

require_once 'ModelePDO.class.php';

class GestionCategorie extends ModelePDO {

    /**
     * Récupère toutes les catégories de la base de données.
     * 
     * @return array Tableau d'objets catégories.
     */
    public static function getLesCategories() {
        self::seConnecter();
        
        // Vérifier que la connexion est établie
        if (self::$pdoCnxBase === null) {
            error_log('ERREUR: Connexion PDO non établie dans getLesCategories()');
            return []; // Retourner un tableau vide au lieu de générer une erreur fatale
        }
        
        try {
            self::$requete = "SELECT * FROM categorie ORDER BY libelle";
            self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
            self::$pdoStResults->execute();
            self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
            self::$pdoStResults->closeCursor();
            
            // Corriger le double encodage UTF-8 si nécessaire
            foreach (self::$resultat as $categorie) {
                if (isset($categorie->libelle)) {
                    $libelle = $categorie->libelle;
                    
                    // Détecter si c'est un double encodage (contient des séquences comme ├«, ├¿, ├®)
                    if (preg_match('/├[«¿®]/u', $libelle)) {
                        // Méthode 1 : Double conversion mb_convert_encoding
                        $libelle_corrige = mb_convert_encoding($libelle, 'ISO-8859-1', 'UTF-8');
                        $libelle_corrige = mb_convert_encoding($libelle_corrige, 'UTF-8', 'ISO-8859-1');
                        
                        // Si ça ne marche pas, essayer iconv
                        if (preg_match('/├[«¿®]/u', $libelle_corrige)) {
                            $libelle_corrige = @iconv('UTF-8', 'ISO-8859-1//IGNORE', $libelle);
                            $libelle_corrige = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $libelle_corrige);
                        }
                        
                        // Si toujours corrompu, utiliser un mapping manuel
                        if (preg_match('/├[«¿®]/u', $libelle_corrige)) {
                            $mapping = [
                                'Bo├«tier' => 'Boîtier',
                                'Carte m├¿re' => 'Carte mère',
                                'M├®moire' => 'Mémoire',
                            ];
                            if (isset($mapping[$libelle])) {
                                $libelle_corrige = $mapping[$libelle];
                            }
                        }
                        
                        $categorie->libelle = $libelle_corrige;
                    }
                }
            }
            
            return self::$resultat;
        } catch (Exception $e) {
            error_log('ERREUR dans getLesCategories(): ' . $e->getMessage());
            return []; // Retourner un tableau vide en cas d'erreur
        }
    }

    /**
     * Récupère une catégorie par son ID.
     * 
     * @param int $id L'ID de la catégorie.
     * @return object La catégorie correspondant à l'ID.
     */
    public static function getCategorieById($id) {
        self::seConnecter();
        self::$requete = "SELECT * FROM categorie WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Ajoute une nouvelle catégorie dans la base de données.
     * 
     * @param string $libelle Le libellé de la catégorie.
     */
    public static function ajouterCategorie($libelle) {
        self::seConnecter();
        self::$requete = "INSERT INTO categorie (libelle) VALUES (:libelle)";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':libelle', $libelle);
        self::$pdoStResults->execute();
    }

    /**
     * Modifie une catégorie dans la base de données.
     *
     * @param int $id L'ID de la catégorie à modifier.
     * @param string $libelle Le nouveau libellé de la catégorie.
     */
    public static function modifierCategorie($id, $libelle) {
        self::seConnecter();
        self::$requete = "UPDATE categorie SET libelle = :libelle WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':libelle', $libelle);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime une catégorie par son libellé.
     * 
     * @param string $libelle Le libellé de la catégorie à supprimer.
     */
    public static function supprimerCategorie($libelle) {
        self::seConnecter();
        self::$requete = "DELETE FROM categorie WHERE libelle = :libelle";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':libelle', $libelle);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime une catégorie par son ID.
     * 
     * @param int $id L'ID de la catégorie à supprimer.
     */
    public static function supprimerCategorieById($id) {
        return self::SupprimerTupleByChamp('categorie', 'id', $id);
    }
}
