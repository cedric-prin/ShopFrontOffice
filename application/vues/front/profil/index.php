<!-- -----------------------------------------------------------------------------
     Fichier : v_profil_client.inc.php
     Rôle    : Vue affichant le profil du client connecté (informations personnelles, sécurité, adresse, nombre de commandes, etc.)
     ----------------------------------------------------------------------------- -->
<link rel="stylesheet" href="<?php echo asset_path('css/pages/profil.css'); ?>">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="profil-container">
    <!-- En-tête du profil avec photo, nom et email -->
    <div class="profil-header">
        <div class="profil-photo">
            <img src="<?php echo asset_path('images/default-avatar.png'); ?>" alt="Photo de profil">
        </div>
        <h1><?php echo !empty($client->prenom) && !empty($client->nom) ? htmlspecialchars($client->prenom . ' ' . $client->nom) : 'Utilisateur'; ?></h1>
        <p><?php echo !empty($client->email) ? htmlspecialchars($client->email) : 'Email non renseigné'; ?></p>
    </div>

    <!-- Message de succès après modification du profil -->
    <?php if (isset($_GET['success']) && $_GET['success'] === 'modification'): ?>
    <div class="alert alert-success">
        <p>Vos informations ont été mises à jour avec succès !</p>
    </div>
    <?php endif; ?>

    <div class="profil-sections-container">
        <!-- Colonne gauche : informations personnelles et sécurité -->
        <div>
            <div class="profil-section">
                <h2>Informations personnelles</h2>
                <!-- Affichage du prénom du client -->
                <div class="info-group">
                    <span class="material-icons info-icon">person</span>
                    <div class="info-content">
                        <label>Prénom</label>
                        <div class="value"><?php echo !empty($client->prenom) ? htmlspecialchars($client->prenom) : 'Non renseigné'; ?></div>
                    </div>
                </div>
                <!-- Affichage du nom du client -->
                <div class="info-group">
                    <span class="material-icons info-icon">badge</span>
                    <div class="info-content">
                        <label>Nom</label>
                        <div class="value"><?php echo !empty($client->nom) ? htmlspecialchars($client->nom) : 'Non renseigné'; ?></div>
                    </div>
                </div>
                <!-- Affichage de la date de naissance du client -->
                <div class="info-group">
                    <span class="material-icons info-icon">cake</span>
                    <div class="info-content">
                        <label>Date de naissance</label>
                        <div class="value"><?php echo !empty($client->date_naissance) ? date('d/m/Y', strtotime($client->date_naissance)) : 'Non renseignée'; ?></div>
                    </div>
                </div>
                <!-- Affichage du téléphone du client -->
                <div class="info-group">
                    <span class="material-icons info-icon">phone</span>
                    <div class="info-content">
                        <label>Téléphone</label>
                        <div class="value"><?php echo !empty($client->tel) ? htmlspecialchars($client->tel) : 'Non renseigné'; ?></div>
                    </div>
                </div>
                <!-- Affichage du nombre de commandes du client (fonction stockée) -->
                <div class="info-group">
                    <span class="material-icons info-icon">shopping_cart</span>
                    <div class="info-content">
                        <label>Nombre de commandes</label>
                        <div class="value"><?php echo isset($nbCommandes) ? htmlspecialchars($nbCommandes) : '0'; ?></div>
                    </div>
                </div>
            </div>

            <div class="profil-section">
                <h2>Sécurité</h2>
                <!-- Affichage de l'email du client -->
                <div class="info-group">
                    <span class="material-icons info-icon">email</span>
                    <div class="info-content">
                        <label>Email</label>
                        <div class="value"><?php echo !empty($client->email) ? htmlspecialchars($client->email) : 'Non renseigné'; ?></div>
                    </div>
                </div>
                <!-- Bloc pour modifier le mot de passe -->
                <div class="info-group">
                    <span class="material-icons info-icon">lock</span>
                    <div class="info-content">
                        <label>Mot de passe</label>
                        <div class="value password-field">
                            <span>••••••••</span>
                            <a href="index.php?controleur=Client&action=afficherModificationProfil" class="btn-modifier-mdp" title="Modifier le mot de passe">
                                <span class="material-icons">edit</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Lien vers la page des commandes du client -->
                <div style="text-align:center;margin: 18px 0 0 0;">
                    <a href="index.php?controleur=Client&action=afficherCommandes" class="btn-primary" style="padding:10px 24px;font-size:1rem;display:inline-block;">&#128179; Mes commandes</a>
                </div>
            </div>
        </div>

        <!-- Colonne droite : adresse du client -->
        <div>
            <div class="profil-section">
                <h2>Adresse</h2>
                <!-- Affichage de la rue -->
                <div class="info-group">
                    <span class="material-icons info-icon">home</span>
                    <div class="info-content">
                        <label>Rue</label>
                        <div class="value"><?php echo !empty($client->rue) ? htmlspecialchars($client->rue) : 'Non renseignée'; ?></div>
                    </div>
                </div>
                <!-- Affichage de la ville -->
                <div class="info-group">
                    <span class="material-icons info-icon">location_city</span>
                    <div class="info-content">
                        <label>Ville</label>
                        <div class="value"><?php echo !empty($client->ville) ? htmlspecialchars($client->ville) : 'Non renseignée'; ?></div>
                    </div>
                </div>
                <!-- Affichage du code postal -->
                <div class="info-group">
                    <span class="material-icons info-icon">markunread_mailbox</span>
                    <div class="info-content">
                        <label>Code postal</label>
                        <div class="value"><?php echo !empty($client->codePostal) ? htmlspecialchars($client->codePostal) : 'Non renseigné'; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton pour modifier les informations du profil -->
        <div class="button-group">
            <a href="index.php?controleur=Client&action=afficherModificationProfil" class="btn-modifier">
                Modifier vos informations
            </a>
        </div>
    </div>
</div>

<!-- Script JS pour la gestion de l'affichage du mot de passe (optionnel ici) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'affichage du mot de passe
    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const input = button.parentElement.querySelector('input');
            const eyeSlash = button.querySelector('.eye-slash');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeSlash.style.display = 'block';
            } else {
                input.type = 'password';
                eyeSlash.style.display = 'none';
            }
        });
    });
});
</script>