<!DOCTYPE html>
<!-- TITRE ET MENUS -->
<html lang="fr">
    <head>
        <title>Prin Boutique</title>
        <meta http-equiv="Content-Language" content="fr">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?php echo asset_path('css/style.css'); ?>" rel="stylesheet"
              type="text/css">
        <link href="<?php echo asset_path('css/components/styleform.css'); ?>" rel="stylesheet" type="text/css">
        <link rel="shortcut icon" type="image/jpeg" href="<?php echo asset_path('images/global/Logo_court.jpg'); ?>">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
        <header class="samsung-header">
            <div class="header-inner">
                <div class="header-left">
                    <div class="header-logo">
                        <a href="index.php"><img src="<?php echo asset_path('images/global/Logo_long.jpg'); ?>"
                                         alt="Prin Boutique" title="Revenir à l'accueil"/></a>
                    </div>
                    <nav class="header-menu">
                        <a href="index.php">Accueil</a>
                        <div class="categories-dropdown">
                            <a href="#" class="dropdown-toggle">Catégories <i class="fas fa-chevron-down"></i></a>
                            <div class="dropdown-menu">
                                <?php 
                                // Récupérer les catégories depuis la base de données (avec gestion d'erreur)
                                try {
                                    $categories = GestionBoutique::getLesCategories();
                                } catch (Exception $e) {
                                    error_log('Erreur lors de la récupération des catégories: ' . $e->getMessage());
                                    $categories = []; // Tableau vide si erreur
                                }
                                foreach ($categories as $categorie): 
                                    $libelle = htmlspecialchars($categorie->libelle);
                                    $lienCategorie = str_replace(' ', '_', $libelle);
                                ?>
                                    <a href="<?php echo asset_path('index.php'); ?>?controleur=Produits&action=afficher&categorie=<?php echo rawurlencode($lienCategorie); ?>"><?php echo $libelle; ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="index.php?controleur=Produits&action=afficherPanier"><i class="fas fa-shopping-cart"></i> Panier</a>
                        <a href="#">Nous contacter</a>
                    </nav>
                </div>
                <div class="header-right">
                    <form class="header-search" method="get" action="index.php">
                        <input type="hidden" name="controleur" value="Produits">
                        <input type="hidden" name="action" value="afficher">
                        <span class="search-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#222" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </span>
                        <input type="text" name="recherche" placeholder="Recherche...">
                    </form>
                    <a href="index.php?controleur=Produits&action=afficherPanier" title="Voir le panier" class="cart-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#222" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <?php $nbArticles = isset($_SESSION['produits']) ? array_sum($_SESSION['produits']) : 0; if ($nbArticles > 0): ?>
                        <span class="badge-panier-header"><?php echo $nbArticles; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="account-dropdown">
                        <a href="#" class="dropdown-toggle">
                            <span class="account-icon">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#222" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="4"/>
                                    <path d="M4 20c0-4 8-6 8-6s8 2 8 6"/>
                                </svg>
                            </span>
                            <?php if (isset($_SESSION['connecte']) && $_SESSION['connecte']): ?>
                                <span class="user-name"><?php echo isset($_SESSION['client_prenom']) ? htmlspecialchars($_SESSION['client_prenom']) : ''; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="account-menu">
                            <?php if (isset($_SESSION['connecte']) && $_SESSION['connecte']): ?>
                                <a href="index.php?controleur=Client&action=afficherProfil">Mon profil</a>
                                <div class="divider"></div>
                                <a href="index.php?controleur=Client&action=afficherCommandes">Mes commandes</a>
                                <div class="divider"></div>
                                <a href="index.php?controleur=Client&action=deconnexion" class="deconnexion">
                                    <i class="fas fa-sign-out-alt"></i> Se déconnecter
                                </a>
                            <?php else: ?>
                                <a href="index.php?controleur=Client&action=afficherConnexion&display=minimal">Se connecter/S'inscrire</a>
                                <div class="divider"></div>
                                <a href="index.php?controleur=Client&action=afficherCommandes">Mes commandes</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </header>

<script>
// Pour le support mobile du menu déroulant
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('active');
        });
    });
});

// Menu déroulant catégories
document.addEventListener('DOMContentLoaded', function() {
    // Catégories
    const catDropdown = document.querySelector('.categories-dropdown');
    if (catDropdown) {
        const toggle = catDropdown.querySelector('.dropdown-toggle');
        const menu = catDropdown.querySelector('.dropdown-menu');
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        });
        document.addEventListener('click', function(e) {
            if (!catDropdown.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    }
    // Compte
    const accDropdown = document.querySelector('.account-dropdown');
    if (accDropdown) {
        const toggle = accDropdown.querySelector('.dropdown-toggle');
        const menu = accDropdown.querySelector('.account-menu');
    }
});
</script>
