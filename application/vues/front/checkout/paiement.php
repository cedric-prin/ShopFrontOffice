<!-- -----------------------------------------------------------------------------
     Fichier : v_checkout_paiement.inc.php
     Rôle    : Vue affichant le formulaire et les informations pour le paiement de la commande.
     ----------------------------------------------------------------------------- -->
<?php

// Debug des données de session
error_log('Données de session dans la vue checkout paiement : ' . print_r($_SESSION, true));
// S'assurer que le panier existe
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
// Calculer le total
$total = array_sum(array_map(function($item) { 
    return $item['prix'] * $item['quantite']; 
}, $_SESSION['panier']));

if (isset($_GET['erreur']) && $_GET['erreur'] === 'date'): ?>
    <div class="alert alert-danger">La date d'expiration de la carte est invalide. Format attendu : MM/AA</div>
<?php endif; ?>

<link rel="stylesheet" href="<?php echo asset_path('css/pages/checkout.css'); ?>">

<div class="checkout-container">
    <div class="checkout-content">
        <div class="section-container">
            <h2 class="numbered active">1. Données personnelles</h2>
            <div class="section-content">
                <div class="info-block">
                    <div class="customer-info">
                        <p><?php echo htmlspecialchars($_SESSION['client_data']['prenom'] . ' ' . $_SESSION['client_data']['nom']); ?></p>
                        <p><?php echo htmlspecialchars($_SESSION['client_data']['rue'] . ', ' . $_SESSION['client_data']['codePostal'] . ', ' . $_SESSION['client_data']['ville'] . ', France'); ?></p>
                    </div>
                    <a href="index.php?controleur=Panier&action=afficherAdresse" class="btn-modifier">Modifier</a>
                </div>
            </div>
        </div>

        <div class="section-container">
            <h2 class="numbered active">2. Options de livraison</h2>
            <div class="section-content">
                <div class="info-block">
                    <div class="customer-info">
                        <p>Colis 1 sur 1</p>
                        <?php if (isset($_SESSION['livraison']) && $_SESSION['livraison']['type'] === 'pickup'): ?>
                            <p><?php echo isset($_SESSION['pickup_point']) ? htmlspecialchars($_SESSION['pickup_point']['name']) : 'Point relais'; ?></p>
                            <p><?php echo isset($_SESSION['pickup_point']) ? htmlspecialchars($_SESSION['pickup_point']['address'] . ', ' . $_SESSION['pickup_point']['postalCode'] . ', ' . $_SESSION['pickup_point']['city']) : ''; ?></p>
                        <?php else: ?>
                            <p>Livraison à domicile</p>
                            <p>Livraison sous 48/72 heures ouvrées après expédition</p>
                        <?php endif; ?>
                    </div>
                    <a href="index.php?controleur=Panier&action=afficherLivraison" class="btn-modifier">Modifier</a>
                </div>
            </div>
        </div>

        <h2 class="numbered active payment-title">3. Paiement</h2>
        
        <form method="post" action="index.php?controleur=Panier&action=validerPaiement" class="payment-form">
            <div class="payment-options">
                <!-- Carte bancaire -->
                <div class="payment-option active">
                    <div class="payment-header">
                        <div class="payment-title">
                            <input type="radio" id="payment-card" name="payment_method" value="card" checked>
                            <span>Carte bancaire</span>
                        </div>
                        <div class="payment-icons">
                            <img src="<?php echo asset_path('images/payment/carte.png'); ?>" alt="Carte bancaire">
                            <span class="payment-arrow"></span>
                        </div>
                    </div>
                    <div class="payment-content" style="display:block;">
                        <div class="form-group">
                            <label for="card-number">Numéro de la carte*</label>
                            <input type="text" id="card-number" name="card_number" placeholder="0000 0000 0000 0000" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="card-name">Nom sur la carte*</label>
                                <input type="text" id="card-name" name="card_name" placeholder="Nom du titulaire" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="card-expiry">Date d'expiration*</label>
                                <input type="text" id="card-expiry" name="card_expiry" placeholder="MM/AA" required>
                            </div>
                            <div class="form-group">
                                <label for="card-cvc">CVC / CVV*</label>
                                <input type="text" id="card-cvc" name="card_cvc" placeholder="123" required>
                            </div>
                        </div>
                        <div class="consent-checkbox">
                            <input type="checkbox" id="terms-consent-card" name="terms_consent" required>
                            <label for="terms-consent-card">En cochant cette case, j'accepte les <a href="#" class="terms-link">Conditions générales de vente du Samsung Shop</a>.</label>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-continuer" id="card-submit-button" disabled>Commander et payer</button>
                        </div>
                    </div>
                </div>
                <!-- PayPal -->
                <div class="payment-option">
                    <div class="payment-header">
                        <div class="payment-title">
                            <input type="radio" id="payment-paypal" name="payment_method" value="paypal">
                            <span>PayPal et PayPal 4x sans frais</span>
                        </div>
                        <div class="payment-icons">
                            <img src="<?php echo asset_path('images/payment/paypalicon.png'); ?>" alt="PayPal">
                            <span class="payment-arrow"></span>
                        </div>
                    </div>
                    <div class="payment-content" style="display:none;">
                        <p>* Champs obligatoires</p>
                        <div class="consent-checkbox">
                            <input type="checkbox" id="terms-consent-paypal" name="terms_consent_paypal">
                            <label for="terms-consent-paypal">En cochant cette case, j'accepte les <a href="#" class="terms-link">Conditions générales</a> de vente du Samsung Shop</label>
                        </div>
                        <div class="options-consent">
                            <div class="consent-checkbox">
                                <input type="checkbox" id="marketing-consent-paypal" name="marketing_consent">
                                <label for="marketing-consent-paypal">Activez cette option pour recevoir par e-mail, SMS et notification push des nouvelles, offres spéciales et recommandations sur nos produits et services.</label>
                            </div>
                        </div>
                        <div class="consent-info">
                            <p>Les informations sont collectées et traitées par Samsung Electronics France et ses prestataires aux fins de gestion de votre commande, notamment pour la livraison et la facturation, et de la connaissance client. Elles sont conservées pendant la durée nécessaire à la réalisation de cette finalité, dans les conditions décrites dans <a href="#" class="link-policy">la Politique de Confidentialité</a>.</p>
                            <p>Vous disposez d'un droit d'accès, de rectification et de suppression des données qui vous concernent. Vous avez également le droit de vous opposer aux traitements réalisés ou d'en demander la limitation. Vous pouvez exercer ces droits en en faisant la demande sur <a href="http://www.samsung.com/request-desk" class="link-policy">http://www.samsung.com/request-desk</a>.</p>
                        </div>
                        <div class="form-actions">
                            <div class="paypal-buttons">
                                <button type="button" class="paypal-button paypal-button-primary paypal-button-disabled" id="paypal-standard-button" disabled>
                                    <img src="<?php echo Chemins::IMAGES; ?>payment/paypalicon.png" alt="PayPal" class="paypal-logo">
                                </button>
                                <button type="button" class="paypal-button paypal-button-secondary paypal-button-disabled" id="paypal-4x-button" disabled>
                                    <div class="paypal-4x">
                                        <img src="<?php echo Chemins::IMAGES; ?>payment/paypalicon.png" alt="PayPal" class="paypal-small-logo">
                                        <span>4X PayPal</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FLOA 3/4x -->
                <div class="payment-option">
                    <div class="payment-header">
                        <div class="payment-title">
                            <input type="radio" id="payment-floa3" name="payment_method" value="floa3">
                            <span>Paiement en 3 ou 4 fois avec FLOA par carte bancaire</span>
                        </div>
                        <div class="payment-icons">
                            <img src="<?php echo asset_path('images/payment/floa3x4x.svg'); ?>" alt="FLOA">
                            <span class="payment-arrow"></span>
                        </div>
                    </div>
                    <div class="payment-content" style="display:none;">
                        <p>Payer en 3 ou 4 fois par carte bancaire grâce à notre partenaire de confiance FLOA. Sous réserve d'acceptation par FLOA. Vous disposez d'un délai légal de rétractation.</p>
                        <div class="floa-options">
                            <div class="floa-option">
                                <input type="radio" id="floa-3x" name="floa_option" value="3x" checked>
                                <label for="floa-3x">3x fois sans frais : <?php echo number_format($total / 3, 2, ',', ' '); ?>€</label>
                            </div>
                            <div class="floa-option">
                                <input type="radio" id="floa-4x" name="floa_option" value="4x">
                                <label for="floa-4x">4x fois sans frais : <?php echo number_format($total / 4, 2, ',', ' '); ?>€</label>
                            </div>
                        </div>
                        <div class="consent-checkbox">
                            <input type="checkbox" id="terms-consent-floa3" name="terms_consent_floa3">
                            <label for="terms-consent-floa3">En cochant cette case, j'accepte les <a href="#" class="terms-link">Conditions générales</a> de vente du Samsung Shop et j'accepte que Samsung transmette mes données à FLOA pour le traitement de ma demande de paiement en plusieurs fois. Plus d'informations <a href="#" class="terms-link">ici</a>.</label>
                        </div>
                        <p class="redirect-notice">En cliquant sur «continuer», vous allez être redirigé sur le formulaire sécurisé FLOA.</p>
                        <div class="form-actions">
                            <button type="button" class="btn-continuer" id="floa3-btn" disabled>Continuer</button>
                        </div>
                    </div>
                </div>
                <!-- FLOA 6-36x -->
                <div class="payment-option">
                    <div class="payment-header">
                        <div class="payment-title">
                            <input type="radio" id="payment-floa6" name="payment_method" value="floa6">
                            <span>Financement de 6 à 36 fois avec FLOA</span>
                        </div>
                        <div class="payment-icons">
                            <img src="<?php echo asset_path('images/payment/floa3x4x.svg'); ?>" alt="FLOA">
                            <span class="payment-arrow"></span>
                        </div>
                    </div>
                    <div class="payment-content" style="display:none;">
                        <p>Payer en 6x, 10x, 12x, 18x, 24x ou 36x grâce à notre partenaire de confiance FLOA.</p>
                        <div class="floa-credit-warning">
                            <p>Un crédit vous engage et doit être remboursé. Vérifiez vos capacités de remboursement avant de vous engager. Sous réserve d'acceptation par FLOA. Vous disposez d'un délai légal de rétractation.</p>
                        </div>
                        <div class="consent-checkbox">
                            <input type="checkbox" id="terms-consent-floa6" name="terms_consent_floa6">
                            <label for="terms-consent-floa6">En cochant cette case, j'accepte les <a href="#" class="terms-link">Conditions générales</a> de vente du Samsung Shop et j'accepte que Samsung transmette mes données à FLOA pour le traitement de ma demande de financement. Plus d'informations <a href="#" class="terms-link">ici</a>.</label>
                        </div>
                        <p class="redirect-notice">En cliquant sur «continuer», vous allez être redirigé sur le formulaire sécurisé FLOA.</p>
                        <div class="form-actions">
                            <button type="button" class="btn-continuer" id="floa6-btn" disabled>Continuer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="checkout-recap">
        <h2>Récapitulatif</h2>
        <div class="recap-header">
            <span>Vous avez <?php echo count($_SESSION['panier']); ?> article(s) dans votre panier</span>
            <a href="index.php?controleur=Produits&action=afficherPanier" class="btn-modifier">Modifier</a>
        </div>

        <div class="recap-produits">
            <?php foreach ($_SESSION['panier'] as $produit): ?>
            <div class="recap-produit">
                <div class="produit-image">
                    <img src="<?php echo asset_path('images/produits/generic/' . $produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                </div>
                <div class="produit-details">
                    <h3><?php echo htmlspecialchars($produit['nom']); ?></h3>
                    <p class="produit-couleur"><?php echo htmlspecialchars($produit['libelle']); ?></p>
                    <div class="produit-prix">
                        <span class="prix-actuel"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="code-promo">
            <div class="code-promo-input">
                <input type="text" placeholder="Saisir les bons ou le code cadeau">
                <button class="btn-appliquer">Appliquer</button>
            </div>
            <a href="#" class="voir-codes">Voir les codes disponibles</a>
        </div>

        <div class="recap-total">
            <div class="total-ttc">
                <span>Total TTC</span>
                <span class="montant"><?php echo number_format($total, 2, ',', ' '); ?> €</span>
            </div>
            <div class="tva">
                Dont TVA : <?php echo number_format($total * 0.2, 2, ',', ' '); ?> €
            </div>
            <div class="paiement-options">
                <div class="paiement-option">
                    <span>3 fois sans frais par carte bancaire</span>
                    <span><?php echo number_format($total / 3, 2, ',', ' '); ?> € x 3</span>
                </div>
                <div class="paiement-option">
                    <span>4 fois sans frais par carte bancaire</span>
                    <span><?php echo number_format($total / 4, 2, ',', ' '); ?> € x 4</span>
                </div>
            </div>
            <div class="financement-info">
                <h4>Financement de 6 à 36 fois</h4>
                <p>Un crédit vous engage et doit être remboursé. Vérifiez vos capacités de remboursement avant de vous engager. Sous réserve d'acceptation par FLOA. Vous disposez d'un délai légal de rétractation. <a href="#" class="learn-more">En savoir plus</a>.</p>
            </div>
            <div class="loyalty-info">
                <p>Merci d'être membre <strong>Blue</strong> du programme Samsung Rewards.</p>
            </div>
        </div>

        <div class="next-steps">
            <h3>Et maintenant ?</h3>
            <p>Un e-mail de confirmation vous a été envoyé à l'adresse associée à votre compte.</p>
            <p>Vous pouvez suivre l'état de votre commande depuis votre espace client.</p>
            <div class="actions">
                <a href="index.php?controleur=Produits&action=afficher" class="btn-primary">Retour à la boutique</a>
                <a href="#" class="btn-secondary">Suivre ma commande</a>
            </div>
        </div>
    </div>
</div>

<script>
// Accordéon et activation des boutons selon la case CGU
function setupPaymentOptions() {
    // Désactiver toutes les sections au chargement
    var options = document.getElementsByClassName('payment-option');
    for (var j = 0; j < options.length; j++) {
        options[j].className = 'payment-option';
        options[j].querySelector('.payment-content').style.display = 'none';
        options[j].querySelector('input[type=radio][name=payment_method]').checked = false;
    }
    // Ouvrir la carte bancaire par défaut
    if (options.length > 0) {
        options[0].className = 'payment-option active';
        options[0].querySelector('.payment-content').style.display = 'block';
        var radio = options[0].querySelector('input[type=radio][name=payment_method]');
        if (radio) radio.checked = true;
    }
    // Gestion des clics sur les headers
    var headers = document.getElementsByClassName('payment-header');
    for (var i = 0; i < headers.length; i++) {
        headers[i].onclick = function() {
            var currentOption = this.parentNode;
            var isActive = currentOption.className.indexOf('active') !== -1;
            // Fermer toutes les sections
            for (var j = 0; j < options.length; j++) {
                options[j].className = 'payment-option';
                options[j].querySelector('.payment-content').style.display = 'none';
                options[j].querySelector('input[type=radio][name=payment_method]').checked = false;
            }
            // Si ce n'était pas actif, ouvrir
            if (!isActive) {
                currentOption.className = 'payment-option active';
                currentOption.querySelector('.payment-content').style.display = 'block';
                var radio = currentOption.querySelector('input[type=radio][name=payment_method]');
                if (radio) radio.checked = true;
            }
            return false;
        };
    }
    // Activation bouton carte bancaire
    var cardSubmitButton = document.getElementById('card-submit-button');
    var cardTermsCheckbox = document.getElementById('terms-consent-card');
    if (cardSubmitButton && cardTermsCheckbox) {
        cardTermsCheckbox.addEventListener('change', function() {
            cardSubmitButton.disabled = !this.checked;
        });
        cardSubmitButton.disabled = !cardTermsCheckbox.checked;
    }
    // Activation boutons PayPal
    var paypalStandardButton = document.getElementById('paypal-standard-button');
    var paypal4xButton = document.getElementById('paypal-4x-button');
    var paypalTermsCheckbox = document.getElementById('terms-consent-paypal');
    if (paypalStandardButton && paypal4xButton && paypalTermsCheckbox) {
        paypalTermsCheckbox.addEventListener('change', function() {
            var enabled = this.checked;
            paypalStandardButton.disabled = !enabled;
            paypal4xButton.disabled = !enabled;
            if (enabled) {
                paypalStandardButton.classList.remove('paypal-button-disabled');
                paypal4xButton.classList.remove('paypal-button-disabled');
            } else {
                paypalStandardButton.classList.add('paypal-button-disabled');
                paypal4xButton.classList.add('paypal-button-disabled');
            }
        });
        paypalStandardButton.disabled = !paypalTermsCheckbox.checked;
        paypal4xButton.disabled = !paypalTermsCheckbox.checked;
    }
    // Activation bouton FLOA 3/4x
    var floa3Btn = document.getElementById('floa3-btn');
    var floa3Terms = document.getElementById('terms-consent-floa3');
    if (floa3Btn && floa3Terms) {
        floa3Terms.addEventListener('change', function() {
            floa3Btn.disabled = !this.checked;
        });
        floa3Btn.disabled = !floa3Terms.checked;
    }
    // Activation bouton FLOA 6-36x
    var floa6Btn = document.getElementById('floa6-btn');
    var floa6Terms = document.getElementById('terms-consent-floa6');
    if (floa6Btn && floa6Terms) {
        floa6Terms.addEventListener('change', function() {
            floa6Btn.disabled = !this.checked;
        });
        floa6Btn.disabled = !floa6Terms.checked;
    }
}
// Exécuter lorsque la page est chargée
if (window.addEventListener) {
    window.addEventListener('load', setupPaymentOptions, false);
} else if (window.attachEvent) {
    window.attachEvent('onload', setupPaymentOptions);
} else {
    window.onload = setupPaymentOptions;
}
// Gérer les boutons PayPal
// (redirection à adapter selon ton besoin)
document.addEventListener('DOMContentLoaded', function() {
    var paypalStandardButton = document.getElementById('paypal-standard-button');
    var paypal4xButton = document.getElementById('paypal-4x-button');
    var paypalTermsCheckbox = document.getElementById('terms-consent-paypal');
    if (paypalStandardButton) {
        paypalStandardButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (paypalTermsCheckbox && paypalTermsCheckbox.checked) {
                window.location.href = 'paypal_retour_direct.php?type=standard';
            } else {
                alert('Veuillez accepter les conditions générales pour continuer.');
            }
        });
    }
    if (paypal4xButton) {
        paypal4xButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (paypalTermsCheckbox && paypalTermsCheckbox.checked) {
                window.location.href = 'paypal_retour_direct.php?type=4x';
            } else {
                alert('Veuillez accepter les conditions générales pour continuer.');
            }
        });
    }
});
</script>