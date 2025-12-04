<!-- -----------------------------------------------------------------------------
     Fichier : v_connexion.inc.php
     Rôle    : Vue affichant le formulaire de connexion à l'espace client.
     ----------------------------------------------------------------------------- -->
<?php

?>
<div class="commandes-container">
    <div class="commandes-title">
        <i class="fas fa-box-open"></i>
        Mes commandes
    </div>
    <?php if (empty($commandes)): ?>
        <div class="commande-empty">Vous n'avez pas encore passé de commande.</div>
    <?php else: ?>
        <?php foreach ($commandes as $commande): ?>
            <div class="commande-card">
                <div class="commande-header">
                    <span class="commande-id">
                        <i class="fas fa-receipt"></i>
                        Commande n°<?= htmlspecialchars($commande->id) ?>
                    </span>
                    <span class="commande-date">Passée le <?= date('d/m/Y à H:i', strtotime($commande->date)) ?></span>
                    <span class="commande-total">Total : <?= number_format($commande->sousTotal, 2, ',', ' ') ?> €</span>
                </div>
                <div class="commande-produits">
                    <?php 
                    $lignes = GestionBoutique::getLignesCommandeById($commande->id);
                    foreach ($lignes as $ligne): ?>
                        <div class="produit-item">
                            <img class="produit-img" src="<?= asset_path('images/produits/generic/' . htmlspecialchars($ligne->image_produit)) ?>" alt="<?= htmlspecialchars($ligne->nom_produit) ?>">
                            <div class="produit-details">
                                <div class="produit-nom"><?= htmlspecialchars($ligne->nom_produit) ?></div>
                                <div class="produit-qte">Quantité : <?= $ligne->quantite ?> &nbsp;|&nbsp; <?= number_format($ligne->prixUnitaire, 2, ',', ' ') ?> €</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div style="text-align:center;margin-top:30px;">
        <a href="index.php?controleur=Produits&action=afficher" class="btn-primary">Retour à la boutique</a>
    </div>
</div> 