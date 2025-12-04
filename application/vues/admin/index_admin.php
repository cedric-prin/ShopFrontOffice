<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Admin</title>
    <link href="<?php echo asset_path('css/pages/styleAdmin2.css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
   <!-- Conteneur principal -->
   <div class="admin-container">
       <!-- Menu à gauche -->
       <div class="admin-menu">
           <div class="user-info">
               <img src="<?php echo asset_path('images/user.png'); ?>" alt="" class="user-photo">
               <span class="user-name"><?php echo $_SESSION['login_admin']; ?></span>
           </div>
           <ul>
                   
               
               <li><a href="index.php?controleur=Admin&action=VoirCategorie&display=minimal" >Gestion Catégorie</a></li>
               <li><a href="#" >Gestion Client</a></li>
               <li><a href="#" >Gestion Produit</a></li>
               <li><a href="index.php?controleur=Admin&action=voirStatsProduits">Statistiques Produits</a></li>
           </ul>
       </div>
       

       <!-- Contenu principal -->
       <div class="admin-content" id="contenu-dynamique">
           <h1>Bienvenue dans l'administration</h1>
           <p>Choisissez une section dans le menu pour commencer.</p>
       </div>
   </div>

    <!-- Bouton de déconnexion séparé du menu, positionné en bas à gauche -->
    <div class="logout-container">
        <a href="index.php?controleur=Admin&action=seDeconnecter" class="btn-deconnexion">Se déconnecter (<?php echo $_SESSION['login_admin'] ?>)</a>
    </div>

     <!-- Script JavaScript -->
<!--    <script>
        function chargerContenu(section) {
            const contenuDynamique = document.getElementById("contenu-dynamique");

            // Requête AJAX pour charger le contenu correspondant
            fetch(`index.php?controleur=Admin&action=${section}`)
                .then(response => response.text())
                .then(data => {
                    contenuDynamique.innerHTML = data;
                })
                .catch(error => {
                    console.error("Erreur lors du chargement du contenu :", error);
                    contenuDynamique.innerHTML = "<p>Erreur lors du chargement du contenu.</p>";
                });
        }
    </script>-->

</body>
</html>