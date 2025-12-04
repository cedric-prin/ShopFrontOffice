<!-- -----------------------------------------------------------------------------
     Fichier : v_checkout_adresse.inc.php
     Rôle    : Vue affichant le formulaire de saisie de l'adresse de livraison lors du passage de commande.
     ----------------------------------------------------------------------------- -->
<?php
// Debug des données de session
error_log('Données de session dans la vue checkout : ' . print_r($_SESSION, true));

// Fonction helper pour afficher les valeurs du client en toute sécurité
function getClientValue($key) {
    if (!isset($_SESSION['client_data'])) {
        error_log("Aucune donnée client en session");
        return '';
    }

    $mapping = [
        'email' => 'email',
        'prenom' => 'prenom',
        'nom' => 'nom',
        'telephone' => 'tel',
        'code_postal' => 'codePostal',
        'ville' => 'ville',
        'adresse' => 'rue'
    ];
    
    $dbKey = $mapping[$key] ?? $key;
    $value = isset($_SESSION['client_data'][$dbKey]) ? $_SESSION['client_data'][$dbKey] : '';
    error_log("Récupération de la valeur pour $key (clé DB: $dbKey): $value");
    return htmlspecialchars($value);
}

// Initialisation du panier
GestionPanier::initialiser();
$produits_panier = GestionPanier::getProduits();
$total = 0;

// Récupérer les détails des produits
$produits_details = [];
foreach ($produits_panier as $id => $quantite) {
    $produit = GestionBoutique::getProduitById($id);
    if ($produit) {
        $produit['quantite'] = $quantite;
        $produits_details[] = $produit;
        $total += $produit['prix'] * $quantite;
    }
}
?>

<link rel="stylesheet" href="<?php echo asset_path('css/pages/checkout.css'); ?>">

<div class="checkout-container">
    <div class="checkout-content">
        <h1>1. Données personnelles</h1>
        
        <form method="post" action="index.php?controleur=Panier&action=validerAdresse" class="form-checkout">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="Votre adresse e-mail" value="<?php 
                    $email = getClientValue('email');
                    echo $email;
                    error_log("Email affiché : $email");
                ?>" readonly required>
            </div>

            <div class="name-row">
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" value="<?php 
                        $prenom = getClientValue('prenom');
                        echo $prenom;
                        error_log("Prénom affiché : $prenom");
                    ?>" required>
                </div>

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Votre nom" value="<?php 
                        $nom = getClientValue('nom');
                        echo $nom;
                        error_log("Nom affiché : $nom");
                    ?>" required>
                </div>
            </div>

            <div class="form-row phone-row">
                <div class="form-group">
                    <label for="prefixe">Préfixe</label>
                    <div class="prefix-input">
                        <select id="prefixe" name="prefixe">
                            <option value="+33" <?php echo (getClientValue('prefixe') === '+33' || !getClientValue('prefixe')) ? 'selected' : ''; ?>>+33 France</option>
                            <option value="+1">+1 États-Unis/Canada</option>
                            <option value="+44">+44 Royaume-Uni</option>
                            <option value="+49">+49 Allemagne</option>
                            <option value="+32">+32 Belgique</option>
                            <option value="+41">+41 Suisse</option>
                            <option value="+352">+352 Luxembourg</option>
                            <option value="+34">+34 Espagne</option>
                            <option value="+39">+39 Italie</option>
                            <option value="+351">+351 Portugal</option>
                            <option value="+31">+31 Pays-Bas</option>
                            <option value="+45">+45 Danemark</option>
                            <option value="+46">+46 Suède</option>
                            <option value="+47">+47 Norvège</option>
                            <option value="+358">+358 Finlande</option>
                            <option value="+43">+43 Autriche</option>
                            <option value="+48">+48 Pologne</option>
                            <option value="+420">+420 République tchèque</option>
                            <option value="+36">+36 Hongrie</option>
                            <option value="+30">+30 Grèce</option>
                            <option value="+40">+40 Roumanie</option>
                            <option value="+7">+7 Russie</option>
                            <option value="+380">+380 Ukraine</option>
                            <option value="+81">+81 Japon</option>
                            <option value="+86">+86 Chine</option>
                            <option value="+82">+82 Corée du Sud</option>
                            <option value="+91">+91 Inde</option>
                            <option value="+61">+61 Australie</option>
                            <option value="+64">+64 Nouvelle-Zélande</option>
                            <option value="+212">+212 Maroc</option>
                            <option value="+213">+213 Algérie</option>
                            <option value="+216">+216 Tunisie</option>
                            <option value="+20">+20 Égypte</option>
                            <option value="+27">+27 Afrique du Sud</option>
                            <option value="+55">+55 Brésil</option>
                            <option value="+52">+52 Mexique</option>
                            <option value="+54">+54 Argentine</option>
                            <option value="+56">+56 Chili</option>
                            <option value="+57">+57 Colombie</option>
                            <option value="+58">+58 Venezuela</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telephone">Numéro de mobile</label>
                    <div class="phone-input">
                        <input type="tel" id="telephone" name="telephone" placeholder="Votre numéro de mobile" value="<?php echo getClientValue('telephone'); ?>" required>
                    </div>
                </div>
            </div>

            <div class="address-section">
                <h2>Adresse de livraison</h2>
                <p class="info-text">L'option de retrait en point relais sera proposée à l'étape suivante (si produits éligibles)</p>

                <div class="form-row postal-row">
                    <div class="form-group">
                        <label for="code_postal">Code postal*</label>
                        <div class="search-input">
                            <span class="search-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="#707070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 21L16.65 16.65" stroke="#707070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input type="text" id="code_postal" name="code_postal" value="<?php echo getClientValue('code_postal'); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ville">Ville*</label>
                        <div class="search-input ville-input">
                            <select id="ville" name="ville" required <?php echo !getClientValue('code_postal') ? 'disabled' : ''; ?>>
                                <?php if (getClientValue('ville')): ?>
                                    <option value="<?php echo getClientValue('ville'); ?>" selected><?php echo getClientValue('ville'); ?></option>
                                <?php else: ?>
                                    <option value="">Sélectionnez votre ville</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="adresse">Numéro et nom de voie*</label>
                    <div class="search-input">
                        <span class="search-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="#707070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21 21L16.65 16.65" stroke="#707070" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input type="text" id="adresse" name="adresse" value="<?php echo getClientValue('adresse'); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="complement">Complément d'adresse (Facultatif)</label>
                    <input type="text" id="complement" name="complement" value="<?php echo getClientValue('complement'); ?>">
                </div>
            </div>

            <div class="billing-section">
                <h2>Informations de facturation</h2>
                <div class="marketing-consent">
                    <div class="marketing-consent-wrapper">
                        <input type="checkbox" name="marketing_consent" value="1" class="marketing-consent-checkbox" id="marketing_consent">
                        <label for="marketing_consent" class="marketing-consent-text">
                            Activez cette option pour recevoir par e-mail, SMS, notification push et WhatsApp, des nouvelles, offres spéciales et recommandations sur nos produits et services.
                            <br><br>
                            Les informations sont collectées et traitées par Samsung Electronics France et ses prestataires aux fins de gestion de votre commande, notamment pour la livraison et la facturation, et de la connaissance client. Elles sont conservées pendant la durée nécessaire à la réalisation de cette finalité, dans les conditions décrites dans <a href="#" class="link-policy">la Politique de Confidentialité</a>.
                            <br><br>
                            Vous disposez d'un droit d'accès, de rectification et de suppression des données qui vous concernent. Vous avez également le droit de vous opposer aux traitements réalisés ou d'en demander la limitation. Vous pouvez exercer ces droits en en faisant la demande sur <a href="http://www.samsung.com/request-desk/" class="link-policy">http://www.samsung.com/request-desk/</a>.
                            <br><br>
                            Lorsque vous souscrivez à un abonnement mobile proposé par un opérateur, certaines données sont collectées et transmises à cet opérateur pour la gestion de votre abonnement.
                            Nous vous invitons à consulter la Politique de confidentialité de votre opérateur pour en savoir plus sur le traitement de vos données pour la gestion de votre abonnement.
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-continuer">Continuer</button>
            </div>
        </form>

        <h2 class="numbered">2. Options de livraison</h2>
        <h2 class="numbered">3. Paiement</h2>

        <div class="payment-section">
            <div class="payment-methods">
                <h3 class="payment-methods-title">Modes de paiement Produits Samsung:</h3>
                <div class="payment-logos">
                    <img src="<?php echo Chemins::IMAGES; ?>payment/visa.png" alt="Visa">
                    <img src="<?php echo Chemins::IMAGES; ?>payment/mastercard.png" alt="Mastercard">
                    <img src="<?php echo Chemins::IMAGES; ?>payment/amex.png" alt="American Express">
                    <img src="<?php echo Chemins::IMAGES; ?>payment/paypal.png" alt="PayPal">
                    <img src="<?php echo Chemins::IMAGES; ?>payment/evollis.png" alt="Evollis">
                </div>
                <p class="payment-notice">Uniquement paiement par carte bancaire pour les produits non-vendus par Samsung</p>
            </div>
        </div>
    </div>

    <div class="checkout-recap">
        <h2>Récapitulatif</h2>
        <div class="recap-header">
            <span>Vous avez <?php echo count($produits_details); ?> article(s) dans votre panier</span>
            <a href="index.php?controleur=Produits&action=afficherPanier" class="btn-modifier">Modifier</a>
        </div>

        <div class="recap-produits">
            <?php foreach ($produits_details as $produit): ?>
            <div class="recap-produit">
                <div class="produit-image">
                    <img src="<?php echo asset_path('images/produits/generic/' . $produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                </div>
                <div class="produit-details">
                    <h3><?php echo htmlspecialchars($produit['nom']); ?></h3>
                    <p class="produit-couleur"><?php echo htmlspecialchars($produit['libelle']); ?></p>
                    <div class="produit-prix">
                        <span class="prix-actuel"><?php echo number_format($produit['prix'], 2, ',', ' '); ?> €</span>
                        <span class="quantite">Quantité : <?php echo $produit['quantite']; ?></span>
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
        </div>
    </div>
</div>

<!-- Ajout du script JavaScript pour la recherche de villes -->
<script src="<?php echo asset_path('js/checkout.js'); ?>"></script> 