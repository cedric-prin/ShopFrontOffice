<!-- -----------------------------------------------------------------------------
     Fichier : v_panier.inc.php
     Rôle    : Vue affichant le contenu du panier de l'utilisateur (liste des produits, quantités, prix, actions sur le panier, etc.)
     ----------------------------------------------------------------------------- -->
<?php

if (GestionPanier::isVide()) {
    echo "
    <div class='panier-vide-samsung-wrapper'>
        <div class='panier-vide-samsung'>
            <div class='panier-vide-icone'>
                <svg width='54' height='54' viewBox='0 0 24 24' fill='none' stroke='#222' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                  <circle cx='9' cy='21' r='1' fill='none' stroke='#222' stroke-width='2'/>
                  <circle cx='20' cy='21' r='1' fill='none' stroke='#222' stroke-width='2'/>
                  <path d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'/>
                </svg>
            </div>
            <h1 class='panier-vide-titre'>Votre panier est vide</h1>
            <p class='panier-vide-soustitre'>
                Connectez-vous à votre compte pour voir vos articles sauvegardés ou continuer à magasiner.
            </p>
            <div class='panier-vide-actions'>
                <a href='index.php?controleur=Produits&action=afficher' class='btn-panier-vide btn-panier-outline'>Poursuivre vos achats</a>";
    if (!isset($_SESSION['connecte']) || !$_SESSION['connecte']) {
        echo "<a href='index.php?controleur=Client&action=afficherConnexion&display=minimal' class='btn-panier-vide btn-panier-blue'>Connexion / Créer un compte</a>";
    }
    echo "</div>
            <div class='bloc-modes-paiement'>
                <h2 class='titre-modes-paiement'>Modes de paiement Produits :</h2>
                <div class='logos-paiement'>
                    <img src='<?php echo asset_path('images/payment/visa.png'); ?>' alt='Visa'>
                    <img src='<?php echo asset_path('images/payment/mastercard.png'); ?>' alt='Mastercard'>
                    <img src='<?php echo asset_path('images/payment/americanexpress.png'); ?>' alt='American Express'>
                    <img src='<?php echo asset_path('images/payment/paypal.png'); ?>' alt='PayPal'>
                    <img src='<?php echo asset_path('images/payment/evollis-tradein.png'); ?>' alt='Evollis'>
                </div>
                <div class='texte-modes-paiement'>
                    Uniquement paiement par carte bancaire pour les produits non-vendus par Samsung
                </div>
                <div class='liens-modes-paiement'>
                    <a href='#'>Politique de retour des produits</a><br>
                    <a href='#'>Modes de livraison des produits</a>
                </div>
            </div>
        </div>
    </div>";
} else {
    $produitsPanier = GestionPanier::getProduits();
?>

<link rel="stylesheet" href="<?php echo asset_path('css/pages/panier.css'); ?>">

<div class="panier-container">
    <div class="panier-produits">
        <h1>Vous avez <?php echo array_sum($produitsPanier); ?> article<?php echo array_sum($produitsPanier) > 1 ? 's' : ''; ?> dans votre panier</h1>
        
        <?php
        $total = 0;
        foreach ($produitsPanier as $idProduit => $quantite) {
            $detailsProduit = GestionBoutique::getProduitById($idProduit);
            
            if ($detailsProduit) {
                $nom = htmlspecialchars($detailsProduit['nom']);
                $prix = (float) htmlspecialchars($detailsProduit['prix']);
                $image = htmlspecialchars($detailsProduit['image']);
                $description = isset($detailsProduit['description']) ? htmlspecialchars($detailsProduit['description']) : '';
                $ref = isset($detailsProduit['reference']) ? htmlspecialchars($detailsProduit['reference']) : '';
                $stock = isset($detailsProduit['QteStockProduit']) && $detailsProduit['QteStockProduit'] > 0
                    ? 'En stock (' . $detailsProduit['QteStockProduit'] . ')'
                    : 'Rupture';
                $prixBarre = isset($detailsProduit['prix_barre']) ? (float)$detailsProduit['prix_barre'] : null;
                $eco = isset($detailsProduit['eco_participation']) ? $detailsProduit['eco_participation'] : '2,54';
                $taxe = isset($detailsProduit['taxe_copie']) ? $detailsProduit['taxe_copie'] : '14,00';
                $sousTotal = $prix * $quantite;
                $total += $sousTotal;
                $economie = ($prixBarre && $prixBarre > $prix) ? number_format($prixBarre - $prix, 2, ',', ' ') : null;
        ?>
                <div class="produit-item-samsung-v4" data-id="<?php echo $idProduit; ?>">
                    <button class="btn-supprimer-samsung-v4" title="Supprimer"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                    <div class="produit-img-samsung-v4">
                        <img src="<?php echo asset_path('images/produits/generic/' . $image); ?>" alt="<?php echo $nom; ?>">
                    </div>
                    <div class="produit-center-samsung-v4">
                        <div class="produit-nom-samsung-v4"><?php echo $nom; ?></div>
                        <?php if ($description) { ?><div class="produit-details-samsung-v4"><?php echo $description; ?></div><?php } ?>
                        <?php if ($ref) { ?><div class="produit-ref-samsung-v4"><?php echo $ref; ?></div><?php } ?>
                        <div class="produit-stock-samsung-v4"><?php echo $stock; ?></div>
                    </div>
                    <div class="produit-prix-samsung-v4">
                        <div class="prix-actuel-samsung-v4"><?php echo number_format($prix, 2, ',', ' '); ?>€</div>
                        <?php if ($prixBarre && $prixBarre > $prix) { ?>
                            <div class="prix-barre-samsung-v4"><?php echo number_format($prixBarre, 2, ',', ' '); ?>€</div>
                            <div class="economie-samsung-v4">Économisez <?php echo $economie; ?>€</div>
                        <?php } ?>
                        <div class="details-prix-samsung-v4">
                            dont <?php echo $eco; ?>€ d'éco-participation<br>
                            dont <?php echo $taxe; ?>€ de taxe à la copie privée
                        </div>
                        <div class="actions-droite-samsung-v4">
                            <button class="btn-quantite-v4 moins">-</button>
                            <input type="number" name="quantites[<?php echo $idProduit; ?>]" value="<?php echo $quantite; ?>" min="1" class="input-quantite-samsung-v4">
                            <button class="btn-quantite-v4 plus">+</button>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>

    <div class="panier-recap">
        <h2 class="recap-titre">Récapitulatif</h2>
        
        <div class="code-promo-samsung">
            <input type="text" placeholder="Code promotionnel">
            <button class="btn-appliquer-samsung">Appliquer</button>
        </div>
        <hr class="recap-separateur-samsung">

        <div class="recap-total-samsung">
            <div class="recap-total-labels">
                <div class="recap-total-ttc">Total TTC</div>
                <div class="recap-tva-label">Dont TVA</div>
            </div>
            <div class="recap-total-montants">
                <div class="recap-total-prix"><?php echo number_format($total, 2, ',', ' '); ?>€</div>
                <div class="recap-tva-prix"><?php echo number_format($total * 0.1667, 2, ',', ' '); ?>€</div>
            </div>
        </div>

        <div class="financement-box-samsung">
            <div class="financement-ligne">
                <span class="financement-label"><b>3 fois sans frais par Carte bancaire</b></span>
                <span class="financement-montant">440,69€ puis 2x 440,68€</span>
            </div>
            <div class="financement-ligne">
                <span class="financement-label"><b>4 fois sans frais par Carte bancaire</b></span>
                <span class="financement-montant">330,52€ puis 3x 330,51€</span>
            </div>
            <hr class="financement-separateur">
            <div class="financement-titres">
                <div><b>Financement de 6 à 36 fois</b></div>
            </div>
            <div class="financement-texte">
                <b>Un crédit vous engage et doit être remboursé.</b> Vérifiez vos capacités de remboursement avant de vous engager. Sous réserve d'acceptation par FLOA. Vous disposez d'un délai légal de rétractation. <a href="#" class="financement-link">En savoir plus.</a>
            </div>
        </div>

        <?php if (!isset($_SESSION['connecte']) || !$_SESSION['connecte']) { ?>
            <a href="index.php?controleur=Client&action=afficherConnexion&display=minimal" class="btn-commander">Continuer</a>
        <?php } else { ?>
            <a href="index.php?controleur=Produits&action=afficherCheckoutAdresse" class="btn-commander">Continuer</a>
        <?php } ?>

        <div class="recap-avantages-samsung">
            <div class="recap-avantage">
                <span class="recap-icone"><img src="<?php echo asset_path('images/icon/icon-free-returns.png'); ?>" alt="Paiement"></span>
                <span>Paiement par carte bancaire en 3 ou 4 fois. <a href="#" class="recap-link">En savoir plus</a></span>
            </div>
            <div class="recap-avantage">
                <span class="recap-icone"><img src="<?php echo asset_path('images/icon/icon-free-delivery.png'); ?>" alt="Livraison offerte"></span>
                <span>Livraison offerte</span>
            </div>
            <div class="recap-avantage">
                <span class="recap-icone"><img src="<?php echo asset_path('images/icon/icon-change-of-mind.png'); ?>" alt="Installation"></span>
                <span>Installation par nos experts</span>
            </div>
        </div>
    </div>
</div>

<!-- Script jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.input-quantite-samsung-v4').on('input', function() {
        var idProduit = $(this).closest('.produit-item-samsung-v4').data('id');
        var nouvelleQuantite = $(this).val();
        var prixUnitaire = parseFloat($(this).closest('.produit-prix-samsung-v4').find('.prix-actuel-samsung-v4').text().replace('€', '').replace(' ', '').replace(',', '.'));
        var sousTotal = prixUnitaire * nouvelleQuantite;
        
        $(this).closest('.produit-item-samsung-v4').find('.produit-sous-total-samsung-v4').text(sousTotal.toFixed(2).replace('.', ',') + ' €');
        updateTotal();

        $.ajax({
            url: 'index.php?controleur=Produits&action=MettreAJourPanier',
            method: 'POST',
            data: {
                quantites: { [idProduit]: nouvelleQuantite }
            },
            success: function(response) {
                var data = JSON.parse(response);
                updateRecap(data.total);
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', status, error);
                alert('Une erreur est survenue. Veuillez réessayer.');
            }
        });
        updateBadgePanier();
    });

    function updateTotal() {
        var total = 0;
        $('.produit-sous-total-samsung-v4').each(function() {
            var sousTotal = parseFloat($(this).text().replace(' €', '').replace(' ', '').replace(',', '.'));
            total += sousTotal;
        });
        updateRecap(total);
    }

    function updateRecap(total) {
        $('.recap-total span:last-child').text(total.toFixed(2).replace('.', ',') + ' €');
    }

    function updateBadgePanier() {
        var total = 0;
        $('.input-quantite-samsung-v4').each(function() {
            var val = parseInt($(this).val());
            if (!isNaN(val)) total += val;
        });
        var $badge = $('.badge-panier-header');
        if ($badge.length) {
            if (total > 0) {
                $badge.text(total);
            } else {
                $badge.remove();
            }
        } else if (total > 0) {
            $('a[title="Voir le panier"]').append('<span class="badge-panier-header">'+total+'</span>');
        }
    }
    updateBadgePanier();

    // Ajout du JS pour les boutons + et - version v4
    $('.btn-quantite-v4.plus').on('click', function() {
        var $input = $(this).siblings('input');
        $input.val(parseInt($input.val()) + 1).trigger('input');
    });
    $('.btn-quantite-v4.moins').on('click', function() {
        var $input = $(this).siblings('input');
        if (parseInt($input.val()) > 1) {
            $input.val(parseInt($input.val()) - 1).trigger('input');
        }
    });
    // Suppression produit version v4
    $('.btn-supprimer-samsung-v4').on('click', function() {
        var idProduit = $(this).closest('.produit-item-samsung-v4').data('id');
        window.location.href = 'index.php?controleur=Produits&action=retirerPanier&idProduit=' + idProduit;
    });
});
</script>

<?php
}
?>
