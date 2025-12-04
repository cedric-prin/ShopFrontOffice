<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration du site</title>
    <link href="<?php echo asset_path('css/pages/styleGestion.css'); ?>" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
    <div class="titre">
        <h1>Administration du site (Accès réservé)</h1>
        <p>- Bonjour <?php echo $_SESSION['login_admin']; ?> -</p>
    </div>

    <?php
    // Inclure la classe gestionCategorie
    require_once Chemins::MODELES . 'GestionCategorie.class.php';

   
   
 
   
   
   
   
    // Récupérer les catégories pour les afficher
    $categories = gestionCategorie::getLesCategories();
    ?>

    <div class="gestion-categorie">
        <h2>Gestion des Catégories</h2>

        <!-- Formulaire pour ajouter une catégorie -->
        <h3>Ajouter une catégorie</h3>
        <form method="POST" action="index.php?controleur=Categories&action=ajouter">
            <label for="libelleCategorie">Nom de la catégorie :</label>
            <input type="text" id="libelleCategorieAjouter" name="libelleCategorie" required>
            <button type="submit" name="ajouter">Ajouter</button>
        </form>

        <!-- Formulaire pour supprimer une catégorie -->
        <h3>Supprimer une catégorie</h3>
        <form method="POST" action="index.php?controleur=Categories&action=supprimer">
            <label for="libelleCategorie">Nom de la catégorie :</label>
            <input type="text" id="libelleCategorieSupprimer" name="libelleCategorie" required>
            <button type="submit" name="supprimer">Supprimer</button>
        </form>

        <!-- Formulaire pour modifier une catégorie -->
        <h3>Modifier une catégorie</h3>
        <form method="POST" action="index.php?controleur=Categories&action=modifier">
            <label for="idCategorie">ID de la catégorie :</label>
            <input type="number" id="idCategorie" name="idCategorie" required>
            <label for="libelleCategorie">Nouveau nom de la catégorie :</label>
            <input type="text" id="libelleCategorieModifier" name="libelleCategorie" required>
            <button type="submit" name="modifier">Modifier</button>
        </form>

        <!-- Affichage des catégories -->
        <h3>Catégories existantes</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom de la catégorie</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $categorie) { ?>
                    <tr>
                        <td><?php echo $categorie->id; ?></td>
                        <td><?php echo $categorie->libelle; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="logout-container">
        <button type="submit" name ="accueil"> Accueil <a href="index.php?controleur=Admin&action=afficherIndex&display=minimal" > (<?php echo $_SESSION['login_admin'] ?>)</a></button>
    </div>

</body>
</html>