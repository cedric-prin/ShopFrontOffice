<!-- -----------------------------------------------------------------------------
     Fichier : v_checkout_confirmation.inc.php
     Rôle    : Vue affichant la confirmation de la commande après paiement.
     ----------------------------------------------------------------------------- -->
<?php 
// Les chemins sont chargés dans bootstrap.php
// require_once __DIR__ . '/../../config/paths.php'; 
?>
<?php
// Vider le panier après paiement
if (isset($_SESSION['panier'])) {
    unset($_SESSION['panier']);
}
?>

<link rel="stylesheet" href="<?php echo asset_path('css/pages/checkout.css'); ?>">

<div class="confirmation-bloc">
    <h2>Merci pour votre commande !</h2>
    <?php if ($commande): ?>
        <p>Commande n° <strong><?php echo htmlspecialchars($commande->id); ?></strong> passée le <?php echo date('d/m/Y H:i', strtotime($commande->date)); ?></p>
    <?php else: ?>
        <p>Commande introuvable.</p>
    <?php endif; ?>
    <h3>Détail de la commande</h3>
    <table class="confirmation-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($lignesCommande)): ?>
                <?php foreach ($lignesCommande as $ligne): ?>
                <tr>
                    <td><img src="<?php echo asset_path('images/produits/generic/' . htmlspecialchars($ligne->image_produit ?? 'default.jpg')); ?>" alt="<?php echo htmlspecialchars($ligne->nom_produit ?? ''); ?>" style="max-width:60px;max-height:60px;"></td>
                    <td><?php echo htmlspecialchars($ligne->nom_produit ?? ''); ?></td>
                    <td><?php echo $ligne->quantite ?? ''; ?></td>
                    <td><?php echo isset($ligne->prixUnitaire) ? number_format($ligne->prixUnitaire, 2, ',', ' ') : ''; ?> €</td>
                    <td><?php echo (isset($ligne->prixUnitaire) && isset($ligne->quantite)) ? number_format($ligne->prixUnitaire * $ligne->quantite, 2, ',', ' ') : ''; ?> €</td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Aucun produit trouvé.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="confirmation-total">
        <span>Total TTC :</span>
        <span class="montant"><?php echo $commande ? number_format($commande->sousTotal, 2, ',', ' ') : '0,00'; ?> €</span>
                </div>
    <h3>Adresse de livraison</h3>
    <?php if (isset($pointRelais) && $pointRelais): ?>
        <div class="livraison-info">
            <strong>Point relais :</strong><br>
            <?php echo htmlspecialchars($pointRelais->prNom); ?><br>
            <?php echo htmlspecialchars($pointRelais->prRue); ?><br>
            <?php echo htmlspecialchars($pointRelais->prCodePostal . ' ' . $pointRelais->prVille); ?>
            </div>
    <?php elseif (isset($adresseLivraison) && $adresseLivraison): ?>
        <div class="livraison-info">
            <strong>Livraison à domicile :</strong><br>
            <?php echo htmlspecialchars($adresseLivraison['prenom'] . ' ' . $adresseLivraison['nom']); ?><br>
            <?php echo htmlspecialchars($adresseLivraison['adresse']); ?><br>
            <?php echo htmlspecialchars($adresseLivraison['code_postal'] . ' ' . $adresseLivraison['ville']); ?>
        </div>
    <?php endif; ?>
    <div class="confirmation-actions">
        <a href="index.php?controleur=Produits&action=afficher" class="btn-primary">Retour à la boutique</a>
        <a href="index.php?controleur=Client&action=afficherCommandes" class="btn-secondary">Voir mes commandes</a>
    </div>
</div> 