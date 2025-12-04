<!-- -----------------------------------------------------------------------------
     Fichier : v_produits.inc.php
     Rôle    : Vue affichant la liste des produits disponibles à la vente.
     ----------------------------------------------------------------------------- -->
<link rel="stylesheet" href="<?php echo asset_path('css/pages/produits-modern.css'); ?>">

<!-- Section Hero Accueil -->
<?php
// Détection de la catégorie
$categorie = isset($_REQUEST['categorie']) ? $_REQUEST['categorie'] : 'all';
$categorieLibelle = str_replace('_', ' ', $categorie);

// Définition du contenu selon la catégorie
$heroContent = [
    'all' => [
        'title' => 'Bienvenue chez Prin Boutique',
        'subtitle' => 'Votre destination pour des composants PC de qualité'
    ],
    'Boîtier' => [
        'title' => 'Boîtiers PC',
        'subtitle' => 'Protégez et organisez vos composants avec nos boîtiers de qualité'
    ],
    'Alimentation' => [
        'title' => 'Alimentations PC',
        'subtitle' => 'Alimentez votre configuration avec des PSU certifiées et performantes'
    ],
    'Disque dur' => [
        'title' => 'Disques durs',
        'subtitle' => 'Stockage fiable et performant pour toutes vos données'
    ],
    'Disque SSD' => [
        'title' => 'SSD et stockage rapide',
        'subtitle' => 'Boostez les performances de votre PC avec nos SSD haute vitesse'
    ],
    'Carte mère' => [
        'title' => 'Cartes mères',
        'subtitle' => 'Le cœur de votre configuration, compatible et performant'
    ],
    'Carte graphique' => [
        'title' => 'Cartes graphiques',
        'subtitle' => 'Puissance graphique pour gaming et création professionnelle'
    ],
    'Mémoire' => [
        'title' => 'Mémoire RAM',
        'subtitle' => 'Optimisez les performances avec nos modules mémoire haute fréquence'
    ],
    'Processeur' => [
        'title' => 'Processeurs',
        'subtitle' => 'Cœurs puissants pour toutes vos applications exigeantes'
    ],
    'Refroidissement' => [
        'title' => 'Refroidissement PC',
        'subtitle' => 'Maintenez votre système au frais avec nos solutions de refroidissement'
    ]
];

$currentContent = isset($heroContent[$categorieLibelle]) ? $heroContent[$categorieLibelle] : $heroContent['all'];
?>
<section class="hero-accueil-section">
    <div class="hero-accueil-content">
        <h1 class="hero-accueil-title"><?php echo htmlspecialchars($currentContent['title']); ?></h1>
        <p class="hero-accueil-subtitle"><?php echo htmlspecialchars($currentContent['subtitle']); ?></p>
        <?php if ($categorie === 'all'): ?>
        <div class="hero-accueil-features">
            <div class="hero-feature">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Livraison rapide</span>
            </div>
            <div class="hero-feature">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span>Garantie qualité</span>
            </div>
            <div class="hero-feature">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <path d="M20 8v6M23 11h-6"/>
                </svg>
                <span>Support client</span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Section Produits -->
<section class="products-section-samsung">
    <div class="container-products-samsung">
        <?php if (empty(App::$lesProduits)): ?>
            <div class="no-products">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                <h2>Aucun produit disponible</h2>
                <p>Revenez bientôt pour découvrir nos nouveautés !</p>
            </div>
        <?php else: ?>
            <div class="products-list-samsung">
                <?php foreach (App::$lesProduits as $unProduit): 
                    // Récupération du stock depuis la base de données
                    $stock = isset($unProduit->QteStockProduit) ? (int)$unProduit->QteStockProduit : 0;
                    // Le produit est en stock uniquement si la quantité est strictement supérieure à 0
                    $enStock = $stock > 0;
                    // Note cohérente basée sur l'ID du produit (toujours la même pour le même produit)
                    $note = 4.5 + (($unProduit->id % 11) / 10); // Note entre 4.5 et 5.5 basée sur l'ID
                    $note = min(5.0, $note); // Limiter à 5 maximum
                    $nbAvis = 1000 + (($unProduit->id * 127) % 9000); // Nombre d'avis cohérent basé sur l'ID
                ?>
                    <article class="product-card-samsung">
                        <div class="product-image-container-samsung">
                            <img 
                                src="<?php echo Chemins::IMAGES_PRODUITS . $unProduit->image; ?>" 
                                alt="<?php echo htmlspecialchars($unProduit->nom); ?>" 
                                class="product-image-samsung"
                                loading="lazy"
                            />
                        </div>
                        
                        <div class="product-content-samsung">
                            <h2 class="product-title-samsung"><?php echo htmlspecialchars($unProduit->nom); ?></h2>
                            
                            <div class="product-rating-samsung">
                                <div class="stars-samsung">
                                    <?php for ($i = 1; $i <= 5; $i++): 
                                        $starValue = $note - ($i - 1);
                                        $isFull = $starValue >= 1;
                                        $isPartial = $starValue > 0 && $starValue < 1;
                                        $fillPercent = $isPartial ? ($starValue * 100) : ($isFull ? 100 : 0);
                                    ?>
                                        <div class="star-container-samsung">
                                            <svg width="16" height="16" viewBox="0 0 24 24" class="star-background-samsung">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="#E0E0E0"/>
                                            </svg>
                                            <?php if ($fillPercent > 0): ?>
                                                <svg width="16" height="16" viewBox="0 0 24 24" class="star-foreground-samsung" style="clip-path: inset(0 <?php echo 100 - $fillPercent; ?>% 0 0);">
                                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="#FFD700"/>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-text-samsung"><?php echo number_format($note, 1, ',', ' '); ?> (<?php echo number_format($nbAvis, 0, ',', ' '); ?>)</span>
                            </div>
                            
                            <div class="product-stock-samsung">
                                <?php if ($enStock): ?>
                                    <span class="stock-badge-samsung in-stock">En stock</span>
                                <?php else: ?>
                                    <span class="stock-badge-samsung out-of-stock">Rupture de stock</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-price-samsung">
                                <span class="price-amount-samsung"><?php echo number_format($unProduit->prix, 2, ',', ' '); ?>€</span>
                            </div>
                            
                            <div class="product-actions-samsung">
                                <form method="post" action="index.php?controleur=Produits&action=AjouterPanier" class="product-form-samsung">
                                    <input type="hidden" name="produitID" value="<?php echo $unProduit->id; ?>">
                                    <input type="hidden" name="quantite" value="1">
                                    <button type="submit" class="btn-acheter-samsung" <?php echo !$enStock ? 'disabled' : ''; ?>>
                                        Acheter
                                    </button>
                                </form>
                                <a href="#" class="btn-decouvrir-samsung" data-product-id="<?php echo $unProduit->id; ?>">
                                    Découvrir
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Feedback visuel lors de l'ajout au panier
    document.querySelectorAll('.product-form-samsung').forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-acheter-samsung');
            if (btn.disabled) {
                e.preventDefault();
                return false;
            }
            
            const originalText = btn.textContent;
            btn.textContent = 'Ajouté !';
            btn.style.background = '#4caf50';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '';
                btn.disabled = false;
            }, 2000);
        });
    });
    
    // Gestion du lien "Découvrir"
    document.querySelectorAll('.btn-decouvrir-samsung').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            // Ici vous pouvez ajouter une modal ou redirection vers une page détail
            console.log('Voir détails produit:', productId);
        });
    });
});
</script>
