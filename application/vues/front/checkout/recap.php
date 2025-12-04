<!-- -----------------------------------------------------------------------------
     Fichier : v_checkout_recap.inc.php
     Rôle    : Vue affichant le récapitulatif de la commande avant paiement.
     ----------------------------------------------------------------------------- -->
<?php
$total = 0;
?>

<div class="checkout-recap">
    <h2 class="recap-titre">Récapitulatif</h2>
    
    <div class="recap-produits">
        <div class="recap-header">
            <span>Vous avez <?php echo count(GestionPanier::getProduits()); ?> articles dans votre panier</span>
            <a href="index.php?controleur=Produits&action=afficherPanier" class="btn-modifier">Modifier</a>
        </div>

        <?php foreach (GestionPanier::getProduits() as $idProduit => $quantite):
            $produit = GestionBoutique::getProduitById($idProduit);
            if ($produit):
                $sousTotal = $produit['prix'] * $quantite;
                $total += $sousTotal;
        ?>
            <div class="recap-produit">
                <img src="<?php echo asset_path('images/produits/generic/' . $produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                <div class="recap-produit-details">
                    <h3><?php echo htmlspecialchars($produit['nom']); ?></h3>
                    <p class="recap-produit-specs"><?php echo htmlspecialchars($produit['description']); ?></p>
                    <div class="recap-produit-prix">
                        <?php if (isset($produit['prix_original']) && $produit['prix_original'] > $produit['prix']): ?>
                            <span class="prix-original"><?php echo number_format($produit['prix_original'], 2, ',', ' '); ?> €</span>
                        <?php endif; ?>
                        <span class="prix-actuel"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</span>
                    </div>
                </div>
            </div>
        <?php 
            endif;
        endforeach;
        $_SESSION['total_commande'] = $total;
        ?>
    </div>

    <div class="recap-calcul">
        <div class="recap-ligne">
            <span>Sous-total</span>
            <span><?php echo number_format($total, 2, ',', ' '); ?> €</span>
        </div>
        <div class="recap-ligne">
            <span>Livraison</span>
            <span>Gratuite</span>
        </div>
        <?php if (isset($_SESSION['code_promo'])): ?>
        <div class="recap-ligne promo">
            <span>Code promo</span>
            <span>-<?php echo number_format($_SESSION['reduction_promo'], 2, ',', ' '); ?> €</span>
        </div>
        <?php endif; ?>
        <div class="recap-total">
            <span>Total TTC</span>
            <span><?php echo number_format($total - (isset($_SESSION['reduction_promo']) ? $_SESSION['reduction_promo'] : 0), 2, ',', ' '); ?> €</span>
        </div>
    </div>

    <div class="recap-paiement">
        <div class="option-paiement">
            <span>3 fois sans frais par carte bancaire</span>
            <span><?php echo number_format($total/3, 2, ',', ' '); ?> € x 3</span>
        </div>
        <div class="option-paiement">
            <span>4 fois sans frais par carte bancaire</span>
            <span><?php echo number_format($total/4, 2, ',', ' '); ?> € x 4</span>
        </div>
    </div>
</div> 