<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques Produits</title>
    <link href="<?php echo asset_path('css/pages/styleAdmin2.css'); ?>" rel="stylesheet" type="text/css">
</head>
<body>
<div class="stats-container">
<h1>Statistiques sur les produits</h1>

<h2>Produits jamais commandés</h2>
<?php if (empty($produitsJamaisCommandes)) : ?>
    <p style="text-align:center;">Tous les produits ont déjà été commandés.</p>
<?php else : ?>
    <table class="stats-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th class="prix-col">Prix</th>
                <th>Stock</th>
                <th class="reappro-col">Réapprovisionner</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produitsJamaisCommandes as $produit) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($produit->nom); ?></td>
                    <td><?php echo htmlspecialchars($produit->description); ?></td>
                    <td class="prix-col"><?php echo number_format($produit->prix, 2, ',', ' '); ?> €</td>
                    <td><?php echo $produit->QteStockProduit; ?></td>
                    <td class="reappro-col">
                        <form method="post" action="index.php?controleur=Admin&action=reapprovisionnerProduit" style="margin:0;display:flex;align-items:center;gap:4px;">
                            <input type="hidden" name="idProduit" value="<?php echo $produit->id; ?>">
                            <input type="number" name="quantite" value="10" min="1" style="width:60px;padding:2px 4px;">
                            <button type="submit" class="stats-back-btn" style="padding:4px 12px;font-size:0.95em;">Réappro.</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if (isset($_SESSION['msg_reappro'])) : ?>
    <p style="color:green;text-align:center;"><b><?php echo $_SESSION['msg_reappro']; unset($_SESSION['msg_reappro']); ?></b></p>
<?php endif; ?>

<h2>Top 5 des produits les plus commandés (en quantité)</h2>
<?php if (empty($topProduitsQte)) : ?>
    <p style="text-align:center;">Aucun produit commandé.</p>
<?php else : ?>
    <table class="stats-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Quantité totale</th>
                <th>Chiffre d'affaires</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topProduitsQte as $produit) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($produit->nom); ?></td>
                    <td><?php echo $produit->totalQte; ?></td>
                    <td><?php echo number_format($produit->totalCA, 2, ',', ' '); ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2>Top 5 des produits les plus commandés (en chiffre d'affaires)</h2>
<?php if (empty($topProduitsCA)) : ?>
    <p style="text-align:center;">Aucun produit commandé.</p>
<?php else : ?>
    <table class="stats-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Chiffre d'affaires</th>
                <th>Quantité totale</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (
                $topProduitsCA as $produit) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($produit->nom); ?></td>
                    <td><?php echo number_format($produit->totalCA, 2, ',', ' '); ?> €</td>
                    <td><?php echo $produit->totalQte; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p style="text-align:center;"><a class="stats-back-btn" href="index.php?controleur=Admin&action=afficherIndex&display=minimal">Retour à l'accueil admin</a></p>
</div>
</body>
</html> 