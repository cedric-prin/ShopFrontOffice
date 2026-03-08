<!-- -----------------------------------------------------------------------------
     Fichier : v_connexion.inc.php
     Rôle    : Vue affichant le formulaire de connexion à l'espace client.
     ----------------------------------------------------------------------------- -->
<?php

$error_message = '';
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    switch($error) {
        case 'champs':
            $error_message = 'Veuillez remplir tous les champs correctement.';
            break;
        case 'connexion':
            $error_message = 'Email ou mot de passe incorrect.';
            break;
        case 'connexion_requise':
            $error_message = 'Vous devez être connecté pour effectuer cette action.';
            break;
        case 'mdp_null':
            $error_message = 'Le mot de passe de ce compte est vide ou non défini. Merci de réinitialiser votre mot de passe ou de contacter le support.';
            break;
        case 'mdp_invalide':
            $error_message = 'Le mot de passe de ce compte est mal encodé (non sécurisé). Merci de réinitialiser votre mot de passe ou de contacter le support.';
            break;
    }
}

$success_message = '';
if (isset($_GET['inscription']) && $_GET['inscription'] === 'success') {
    $success_message = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
}

// Récupérer les valeurs des cookies si elles existent
$remembered_email = isset($_COOKIE['remembered_email']) ? $_COOKIE['remembered_email'] : '';
$remembered_password = isset($_COOKIE['remembered_password']) ? base64_decode($_COOKIE['remembered_password']) : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter - Prin Boutique</title>
    <link rel="stylesheet" href="<?php echo Chemins::CSS; ?>pages/auth.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <h1>Un compte. N'importe quel appareil.</h1>
            <h2>Juste pour vous.</h2>
            <p>Connectez-vous pour démarrer</p>
        </div>

        <form class="auth-form" action="/client/traiterConnexion" method="post">
            <div class="form-group">
                <input type="email" id="email" name="email" class="form-control<?php echo $remembered_email ? ' has-value' : ''; ?>" required value="<?php echo htmlspecialchars($remembered_email); ?>">
                <label for="email">Adresse e-mail</label>
            </div>
            
            <div class="form-group password-field">
                <input type="password" id="mdp" name="mdp" class="form-control<?php echo $remembered_password ? ' has-value' : ''; ?>" required value="<?php echo htmlspecialchars($remembered_password); ?>">
                <label for="mdp">Mot de passe</label>
                <button type="button" class="password-toggle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5c-7.333 0-12 6-12 6s4.667 6 12 6 12-6 12-6-4.667-6-12-6z"/>
                        <circle cx="12" cy="11" r="3"/>
                    </svg>
                </button>
            </div>

            <div class="form-check">
                <input type="checkbox" id="remember" name="remember" class="form-check-input" <?php echo $remembered_email ? 'checked' : ''; ?>>
                <label for="remember" class="form-check-label">Mémoriser mon ID</label>
            </div>

            <button type="submit" class="btn btn-next btn-full">Suivant</button>

            <div class="auth-links">
                <a href="/client/inscription" class="link">Créer un compte</a>
            </div>

            <div class="social-login">
                <button type="button" class="btn btn-social btn-google">
                    <svg width="24" height="24" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Se connecter avec Google
                </button>
                <button type="button" class="btn btn-social btn-qr">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3h7v7H3z"/>
                        <path d="M14 3h7v7h-7z"/>
                        <path d="M3 14h7v7H3z"/>
                        <rect x="14" y="14" width="7" height="7"/>
                        <path d="M6 6h1v1H6z"/>
                        <path d="M17 6h1v1h-1z"/>
                        <path d="M6 17h1v1H6z"/>
                        <path d="M17 17h1v1h-1z"/>
                    </svg>
                    Se connecter avec code QR
                </button>
            </div>

            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'affichage du mot de passe
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const input = button.previousElementSibling;
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;
            });
        });

        // Gestion des champs avec label flottant
        document.querySelectorAll('.form-control').forEach(input => {
            // Vérifier l'état initial
            if (input.value) {
                input.classList.add('has-value');
            }

            // Gérer les changements
            input.addEventListener('input', function() {
                if (this.value) {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }
            });

            // Gérer le cas où l'utilisateur remplit un champ puis rafraîchit la page
            input.addEventListener('change', function() {
                if (this.value) {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }
            });
        });
    });
    </script>
</body>
</html> 