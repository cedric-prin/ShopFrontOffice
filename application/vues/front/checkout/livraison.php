<!-- -----------------------------------------------------------------------------
     Fichier : v_checkout_livraison.inc.php
     Rôle    : Vue affichant le choix du mode de livraison (domicile, point relais, etc.) lors du passage de commande.
     ----------------------------------------------------------------------------- -->
<?php
// Debug des données de session
error_log('Données de session dans la vue checkout livraison : ' . print_r($_SESSION, true));

// S'assurer que le panier existe
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
?>

<link rel="stylesheet" href="<?php echo asset_path('css/pages/checkout.css'); ?>">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="checkout-container">
    <div class="checkout-content">
        <div class="personal-info-summary">
            <h2 class="numbered active">1. Données personnelles</h2>
            <div class="info-block">
                <div class="customer-info">
                    <p><?php echo htmlspecialchars($_SESSION['client_data']['prenom'] . ' ' . $_SESSION['client_data']['nom']); ?></p>
                    <p><?php echo htmlspecialchars($_SESSION['client_data']['rue'] . ', ' . $_SESSION['client_data']['codePostal'] . ', ' . $_SESSION['client_data']['ville'] . ', France'); ?></p>
                </div>
                <a href="index.php?controleur=Panier&action=afficherAdresse" class="btn-modifier">Modifier</a>
            </div>
        </div>

        <h2 class="numbered active">2. Options de livraison</h2>
        <h3 class="delivery-title">Comment souhaitez-vous recevoir votre colis ?</h3>

        <form method="post" action="index.php?controleur=Panier&action=validerLivraison" class="delivery-form" id="main-delivery-form">
            <?php if (isset($_SESSION['erreurs']) && !empty($_SESSION['erreurs'])): ?>
            <div class="error-messages">
                <?php foreach ($_SESSION['erreurs'] as $erreur): ?>
                    <p class="error-message"><?php echo htmlspecialchars($erreur); ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['erreurs']); ?>
            </div>
            <?php endif; ?>
            
            <div class="delivery-options">
                <div class="delivery-option">
                    <input type="radio" id="home-delivery" name="delivery_type" value="home" checked>
                    <label for="home-delivery" class="delivery-label">
                        <span class="delivery-icon">🏠</span>
                        <span>À domicile</span>
                    </label>
                            </div>

                <div class="delivery-option">
                    <input type="radio" id="pickup-delivery" name="delivery_type" value="pickup">
                    <label for="pickup-delivery" class="delivery-label">
                        <span class="delivery-icon">🚚</span>
                        <span>Point relais</span>
                    </label>
                </div>
            </div>

            <p class="package-count">Colis 1 sur 1</p>
            
            <?php foreach ($_SESSION['panier'] as $produit): ?>
            <div class="product-delivery-info">
                <img src="<?php echo asset_path('images/produits/generic/' . $produit['image']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>" class="product-thumbnail">
                <div class="product-details">
                            <h4><?php echo htmlspecialchars($produit['nom']); ?></h4>
                    <p class="product-color"><?php echo htmlspecialchars($produit['libelle']); ?></p>
                    <div class="price-info">
                        <?php if (isset($produit['prix_original']) && $produit['prix_original'] > $produit['prix']): ?>
                        <span class="original-price"><?php echo number_format($produit['prix_original'], 2, ',', ' '); ?>€</span>
                        <?php endif; ?>
                        <span class="current-price"><?php echo number_format($produit['prix'], 2, ',', ' '); ?>€</span>
                    </div>
                    <p class="delivery-date">Livraison à partir du <?php echo date('d/m/Y', strtotime('+2 days')); ?></p>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Livraison à domicile (affiché par défaut) -->
            <div id="home-delivery-info" class="delivery-option-content">
                <div class="delivery-info">
                    <div class="delivery-option-details">
                        <div class="pointrelais-info">
                            <span class="delivery-icon">🚚</span>
                            <p>Livraison sous 48/72 heures ouvrées après expédition</p>
                            <span class="delivery-price">OFFERTE</span>
                        </div>
                    </div>
                    <a href="#" class="learn-more">En savoir plus</a>
                </div>
                
                <!-- Bouton simplifié pour la livraison à domicile -->
                <div class="home-delivery-action">
                    <button type="button" class="btn-continuer-domicile btn-continuer">
                        Continuer
                    </button>
                </div>
            </div>

            <!-- Point relais (caché par défaut) -->
            <div id="pickup-delivery-info" class="delivery-option-content" style="display: none;">
                <div class="pickup-location">
                    <h3>Localiser un point de retrait</h3>
                    <div class="pickup-search">
                        <div class="postal-search">
                            <label for="postal-code">Code postal*</label>
                            <input type="text" id="postal-code" name="postal_code" placeholder="Entrez votre code postal" required>
                        </div>
                        <div class="city-search">
                            <label for="city">Ville*</label>
                            <select id="city" name="city" required disabled>
                                <option value="">Sélectionnez votre ville</option>
                            </select>
                        </div>
                        <button type="button" class="btn-search-pickup" disabled><span class="map-icon">🗺️</span></button>
                    </div>
                    <div id="pickup-error" class="error-message" style="display: none;">
                        Veuillez entrer un code postal valide.
                    </div>
                    
                    <!-- Résultats des points relais (caché par défaut) -->
                    <div id="pickup-results" class="pickup-results" style="display: none;">
                        <h4>Points relais à proximité</h4>
                        <div class="pickup-points-list">
                            <!-- Les points relais seront ajoutés ici dynamiquement -->
                        </div>
                    </div>
                    
                    <p class="pickup-info">Pour récupérer votre commande, il est nécessaire que vous présentiez une pièce d'identité.</p>
                    <p class="pickup-info">Si la personne qui fait le retrait de la commande est différente de la personne qui a effectué l'achat, une autorisation écrite et une copie de la carte d'identité de l'acheteur devront être présentées.</p>
                </div>

                <!-- Bouton pour point relais -->
                <div class="pickup-actions">
                    <button type="submit" id="submit-pickup" class="btn-pickup-submit" disabled>Valider ce point relais et continuer</button>
                </div>
            </div>

            <!-- Champs cachés pour infos point relais -->
            <input type="hidden" name="prNom" id="prNom-hidden">
            <input type="hidden" name="prRue" id="prRue-hidden">
            <input type="hidden" name="prCodePostal" id="prCodePostal-hidden">
            <input type="hidden" name="prVille" id="prVille-hidden">
        </form>

        <h2 class="numbered">3. Paiement</h2>
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
                    <img src="<?php echo Chemins::IMAGES_PRODUITS . $produit['image']; ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
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
                <span class="montant"><?php 
                    $total = array_sum(array_map(function($item) { 
                        return $item['prix'] * $item['quantite']; 
                    }, $_SESSION['panier']));
                    echo number_format($total, 2, ',', ' '); 
                ?> €</span>
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

<script>
$(document).ready(function() {
    // Validation du formulaire de livraison à domicile à la soumission
    $('.home-delivery-form').on('submit', function(e) {
        console.log('Formulaire de livraison à domicile soumis');
        // On soumet directement, pas besoin d'autre validation pour l'option à domicile
        return true;
    });
    
    // Variable pour suivre si un point relais est sélectionné
    let isPointRelaisSelected = false;
    
    // Gestion du changement d'option de livraison
    $('input[name="delivery_type"]').change(function() {
        if ($(this).val() === 'home') {
            $('#home-delivery-info').show();
            $('#pickup-delivery-info').hide();
            isPointRelaisSelected = false;
        } else {
            $('#home-delivery-info').hide();
            $('#pickup-delivery-info').show();
            // Vérifier si un point relais est déjà sélectionné
            isPointRelaisSelected = $('input[name="pickup_point"]:checked').length > 0;
        }
    });

    // Suivi de la sélection des points relais
    $(document).on('change', 'input[name="pickup_point"]', function() {
        isPointRelaisSelected = true;
        // Activer le bouton de soumission du point relais
        $('#submit-pickup').prop('disabled', false);

        // Récupère les infos du point relais sélectionné
        var $radio = $(this);
        $('#prNom-hidden').val($radio.data('nom'));
        $('#prRue-hidden').val($radio.data('rue'));
        $('#prCodePostal-hidden').val($radio.data('cp'));
        $('#prVille-hidden').val($radio.data('ville'));
    });
    
    // Gestion du bouton de validation du point relais
    $('#submit-pickup').on('click', function(e) {
        // Coche le radio 'pickup' avant de soumettre
        $('#pickup-delivery').prop('checked', true);
        // Vérifie qu'un point relais est sélectionné
        if (!$('input[name="pickup_point"]:checked').val()) {
            alert('Veuillez sélectionner un point relais avant de continuer.');
            e.preventDefault();
            return false;
        }
        // Log la valeur du champ radio
        console.log('Clic sur bouton point relais, delivery_type sélectionné :', $('input[name="delivery_type"]:checked').val());
        // Soumission native du formulaire (plus robuste)
        document.getElementById('main-delivery-form').submit();
    });

    // Gestion de la recherche de code postal
    $('#postal-code').on('input', function() {
        const postalCode = $(this).val().trim();
        const citySelect = $('#city');
        const searchButton = $('.btn-search-pickup');
        
        // Réinitialiser le select
        citySelect.html('<option value="">Sélectionnez votre ville</option>');
        citySelect.prop('disabled', true);
        searchButton.prop('disabled', true);
        $('#pickup-error').hide();
        $('#pickup-results').hide();
        
        // Vérifier si le code postal a 5 chiffres
        if (postalCode.length === 5 && /^\d+$/.test(postalCode)) {
            // Simuler la recherche (normalement, on ferait un appel API)
            searchCitiesByPostalCode(postalCode, citySelect, searchButton);
        }
    });

    // Activer le bouton de recherche quand une ville est sélectionnée
    $('#city').change(function() {
        if ($(this).val()) {
            $('.btn-search-pickup').prop('disabled', false);
            $('#pickup-results').hide();
        } else {
            $('.btn-search-pickup').prop('disabled', true);
        }
    });
    
    // Gestion du clic sur le bouton de recherche de points relais
    $('.btn-search-pickup').click(function() {
        const postalCode = $('#postal-code').val();
        const city = $('#city').val();
        
        if (postalCode && city) {
            searchPickupPoints(postalCode, city);
        }
    });

    // Fonction de recherche des villes par code postal
    function searchCitiesByPostalCode(postalCode, citySelect, searchButton) {
        // Simuler un chargement
        citySelect.html('<option value="">Chargement...</option>');
        
        // En production, remplacer par un vrai appel API
        // Par exemple: fetch(`https://api-adresse.data.gouv.fr/search/?q=${postalCode}&type=municipality&limit=10`)
        
        // Simulation pour démonstration
        setTimeout(function() {
            // Liste des codes postaux et villes associées
            const codesPostaux = {
                "75001": ["Paris 1er"],
                "75002": ["Paris 2e"],
                "75003": ["Paris 3e"],
                "75004": ["Paris 4e"],
                "75005": ["Paris 5e"],
                "13001": ["Marseille 1er"],
                "13002": ["Marseille 2e"],
                "13003": ["Marseille 3e"],
                "69001": ["Lyon 1er"],
                "69002": ["Lyon 2e"],
                "69003": ["Lyon 3e"],
                "34000": ["Montpellier"],
                "34070": ["Montpellier"],
                "34080": ["Montpellier"],
                "34090": ["Montpellier"],
                "34980": ["Saint-Gély-du-Fesc", "Saint-Clément-de-Rivière"],
                "34920": ["Le Crès"],
                "34170": ["Castelnau-le-Lez"],
                "34470": ["Pérols"],
                "34250": ["Palavas-les-Flots"],
                "34280": ["La Grande-Motte"],
                "31000": ["Toulouse"],
                "33000": ["Bordeaux"],
                "44000": ["Nantes"],
                "59000": ["Lille"],
                "67000": ["Strasbourg"],
                "06000": ["Nice"],
                "35000": ["Rennes"],
                "51100": ["Reims"],
                "76000": ["Rouen"],
                "38000": ["Grenoble"],
                "21000": ["Dijon"],
                "37000": ["Tours"],
                "14000": ["Caen"],
                "87000": ["Limoges"],
                "63000": ["Clermont-Ferrand"],
                "29000": ["Quimper"],
                "84000": ["Avignon"]
            };
            
            // Vérifier si le code postal est dans notre liste
            if (codesPostaux[postalCode]) {
                let options = '<option value="">Sélectionnez votre ville</option>';
                codesPostaux[postalCode].forEach(ville => {
                    options += `<option value="${ville}">${ville}</option>`;
                });
                citySelect.html(options);
                citySelect.prop('disabled', false);
            }
            // Si le code postal n'est pas dans notre liste mais a un format valide (5 chiffres)
            else if (/^\d{5}$/.test(postalCode)) {
                // On accepte quand même le code postal et on propose une ville générique
                citySelect.html(`
                    <option value="">Sélectionnez votre ville</option>
                    <option value="Ville (${postalCode})">Ville (${postalCode})</option>
                `);
                citySelect.prop('disabled', false);
            }
            // Code postal invalide
            else {
                citySelect.html('<option value="">Aucune ville trouvée</option>');
                $('#pickup-error').show();
            }
        }, 500); // Délai simulé de 500ms
    }
    
    // Fonction de recherche des points relais
    function searchPickupPoints(postalCode, city) {
        // Afficher un message de chargement
        $('.pickup-points-list').html('<p>Recherche des points relais à proximité...</p>');
        $('#pickup-results').show();
        
        // En production, remplacer par un vrai appel API pour trouver les points relais
        // Par exemple: fetch(`https://api-points-relais.fr/search?postal_code=${postalCode}&city=${encodeURIComponent(city)}`)
        
        // Simulation pour démonstration
        setTimeout(function() {
            let pointsRelais = [];
            
            // Générer des points relais fictifs selon la ville
            if (city === "Paris 1er") {
                pointsRelais = [
                    { id: 1, name: "Relay Châtelet", address: "4 Rue de Rivoli", postalCode: "75001", city: "Paris", distance: "0.5 km", hours: "Lun-Sam: 7h-22h, Dim: 8h-21h" },
                    { id: 2, name: "Tabac du Louvre", address: "24 Rue de Rivoli", postalCode: "75001", city: "Paris", distance: "0.8 km", hours: "Lun-Ven: 7h30-20h, Sam: 9h-19h" },
                    { id: 3, name: "Librairie Palais Royal", address: "8 Rue de Montpensier", postalCode: "75001", city: "Paris", distance: "1.2 km", hours: "Lun-Sam: 10h-19h" },
                    { id: 4, name: "Espace Presse Tuileries", address: "210 Rue de Rivoli", postalCode: "75001", city: "Paris", distance: "1.5 km", hours: "Lun-Dim: 8h-20h" }
                ];
            } 
            else if (city === "Montpellier") {
                pointsRelais = [
                    { id: 1, name: "Tabac Comédie", address: "12 Place de la Comédie", postalCode: "34000", city: "Montpellier", distance: "0.3 km", hours: "Lun-Sam: 7h-20h, Dim: 9h-19h" },
                    { id: 2, name: "Relay Gare Saint-Roch", address: "Gare SNCF", postalCode: "34000", city: "Montpellier", distance: "0.7 km", hours: "Lun-Dim: 6h-22h" },
                    { id: 3, name: "Tabac du Polygone", address: "Centre Commercial Polygone", postalCode: "34000", city: "Montpellier", distance: "1.1 km", hours: "Lun-Sam: 8h30-20h30" },
                    { id: 4, name: "Librairie Sauramps", address: "Le Triangle", postalCode: "34000", city: "Montpellier", distance: "1.4 km", hours: "Lun-Sam: 9h-19h" },
                    { id: 5, name: "Tabac Antigone", address: "45 Place du Nombre d'Or", postalCode: "34000", city: "Montpellier", distance: "1.8 km", hours: "Lun-Sam: 7h30-19h30, Dim: 8h-13h" }
                ];
            }
            else if (city === "Toulouse") {
                pointsRelais = [
                    { id: 1, name: "Relay Capitole", address: "8 Place du Capitole", postalCode: "31000", city: "Toulouse", distance: "0.2 km", hours: "Lun-Sam: 8h-20h, Dim: 9h-18h" },
                    { id: 2, name: "Tabac Wilson", address: "12 Place Wilson", postalCode: "31000", city: "Toulouse", distance: "0.6 km", hours: "Lun-Sam: 7h30-19h30" },
                    { id: 3, name: "Tabac Jean Jaurès", address: "45 Allée Jean Jaurès", postalCode: "31000", city: "Toulouse", distance: "1.0 km", hours: "Lun-Dim: 7h-22h" }
                ];
            }
            else {
                // Pour les autres villes, générer des points relais génériques
                pointsRelais = [
                    { id: 1, name: "Point Relais Centre-Ville", address: "15 Rue Principale", postalCode: postalCode, city: city, distance: "0.4 km", hours: "Lun-Sam: 9h-19h" },
                    { id: 2, name: "Tabac-Presse du Marché", address: "3 Place du Marché", postalCode: postalCode, city: city, distance: "0.9 km", hours: "Lun-Sam: 7h30-19h30, Dim: 8h-12h" },
                    { id: 3, name: "Librairie Centrale", address: "28 Avenue de la République", postalCode: postalCode, city: city, distance: "1.3 km", hours: "Lun-Sam: 10h-19h" }
                ];
            }
            
            // Afficher les points relais
            if (pointsRelais.length > 0) {
                let html = '';
                pointsRelais.forEach(point => {
                    html += `
                    <div class="pickup-point">
                        <div class="pickup-point-radio">
                            <input type="radio" name="pickup_point" id="pickup-${point.id}" value="${point.id}" data-nom="${point.name}" data-rue="${point.address}" data-cp="${point.postalCode}" data-ville="${point.city}">
                            <label for="pickup-${point.id}"></label>
                        </div>
                        <div class="pickup-point-info">
                            <h5>${point.name}</h5>
                            <p>${point.address}</p>
                            <p>${point.postalCode} ${point.city}</p>
                            <p class="distance"><strong>Distance:</strong> ${point.distance}</p>
                            <p class="hours"><strong>Horaires:</strong> ${point.hours}</p>
                        </div>
                    </div> 
                    `;
                });
                $('.pickup-points-list').html(html);
            } else {
                $('.pickup-points-list').html('<p>Aucun point relais trouvé dans un rayon de 10 km.</p>');
            }
        }, 1000); // Délai simulé de 1 seconde
    }

    // Gestion du bouton continuer pour la livraison à domicile
    $('.btn-continuer-domicile').on('click', function(e) {
        console.log('Clic sur bouton domicile');
        $('#home-delivery').prop('checked', true);
        // Soumission native du formulaire (plus robuste)
        document.getElementById('main-delivery-form').submit();
    });
});
</script> 