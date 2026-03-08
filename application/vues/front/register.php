<!-- -----------------------------------------------------------------------------
     Fichier : v_register.inc.php
     Rôle    : Vue affichant le formulaire d'inscription d'un nouvel utilisateur/client.
     ----------------------------------------------------------------------------- -->
<?php
$error_message = '';
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    switch($error) {
        case 'champs':
            $error_message = 'Veuillez remplir tous les champs correctement.';
            break;
        case 'inscription':
            $error_message = 'Une erreur est survenue lors de l\'inscription.';
            break;
        case 'mdp':
            $error_message = 'Les mots de passe ne correspondent pas.';
            break;
        case 'email_existe':
            $error_message = 'Cet email est déjà utilisé.';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - Prin Boutique</title>
    <link rel="stylesheet" href="<?php echo Chemins::CSS; ?>pages/auth.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h1 class="auth-title">Créer votre compte</h1>

        <form class="auth-form" action="/client/traiterInscription" method="post">
                <div class="form-group">
                <input type="email" id="email" name="email" class="form-control" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    <label for="email">E-mail</label>
                <p class="error-message" id="email-error">L'adresse e-mail n'est pas valide.</p>
                <p class="error-message" id="email-empty">Veuillez remplir ce champ</p>
                </div>
                
                <div class="form-group password-field">
                <input type="password" id="mdp" name="mdp" class="form-control" required 
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$">
                <label for="mdp">Mot de passe</label>
                    <button type="button" class="password-toggle">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5c-7.333 0-12 6-12 6s4.667 6 12 6 12-6 12-6-4.667-6-12-6z"/>
                            <circle cx="12" cy="11" r="3"/>
                        </svg>
                    </button>
                <p class="error-message" id="mdp-error">Utilisez au moins 8 caractères avec un mélange de lettres, de chiffres et de symboles.</p>
                <p class="error-message" id="mdp-empty">Veuillez remplir ce champ</p>
                </div>

                <div class="form-group password-field">
                <input type="password" id="mdp_confirm" name="mdp_confirm" class="form-control" required>
                <label for="mdp_confirm">Confirmer mot de passe</label>
                    <button type="button" class="password-toggle">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5c-7.333 0-12 6-12 6s4.667 6 12 6 12-6 12-6-4.667-6-12-6z"/>
                            <circle cx="12" cy="11" r="3"/>
                        </svg>
                    </button>
                <p class="error-message" id="mdp-confirm-error">Les mots de passe ne correspondent pas.</p>
                <p class="error-message" id="mdp-confirm-empty">Veuillez remplir ce champ</p>
                </div>

                <div class="form-group">
                <input type="text" id="prenom" name="prenom" class="form-control" required pattern="[A-Za-zÀ-ÿ-\s]{2,}">
                    <label for="prenom">Prénom</label>
                <p class="error-message" id="prenom-error">Veuillez saisir votre prénom</p>
                <p class="error-message" id="prenom-empty">Veuillez remplir ce champ</p>
                </div>

                <div class="form-group">
                <input type="text" id="nom" name="nom" class="form-control" required pattern="[A-Za-zÀ-ÿ-\s]{2,}">
                    <label for="nom">Nom</label>
                <p class="error-message" id="nom-error">Veuillez saisir votre nom</p>
                <p class="error-message" id="nom-empty">Veuillez remplir ce champ</p>
                </div>

            <p class="date-label">Date de naissance</p>
            <div class="form-row date-inputs">
                <div class="form-group">
                    <input type="text" inputmode="numeric" pattern="[0-9]*" id="jour" name="jour" class="form-control" min="1" max="31" maxlength="2" required>
                    <label for="jour">Jour</label>
                </div>

                <div class="form-group">
                    <select id="mois" name="mois" class="form-control" required>
                        <option value=""></option>
                        <option value="1">Janvier</option>
                        <option value="2">Février</option>
                        <option value="3">Mars</option>
                        <option value="4">Avril</option>
                        <option value="5">Mai</option>
                        <option value="6">Juin</option>
                        <option value="7">Juillet</option>
                        <option value="8">Août</option>
                        <option value="9">Septembre</option>
                        <option value="10">Octobre</option>
                        <option value="11">Novembre</option>
                        <option value="12">Décembre</option>
                    </select>
                    <label for="mois">Mois</label>
                </div>

                <div class="form-group">
                    <input type="text" inputmode="numeric" pattern="[0-9]{4}" id="annee" name="annee" class="form-control" maxlength="4" required>
                    <label for="annee">Année</label>
                </div>
                <p class="error-message" id="jour-empty">Veuillez remplir ce champ</p>
                <p class="error-message" id="mois-empty">Veuillez remplir ce champ</p>
                <p class="error-message" id="annee-empty">Veuillez remplir ce champ</p>
                <p class="error-message" id="jour-error">Le jour doit être un nombre entre 1 et 31</p>
                </div>

                <div class="button-group">
                <a href="/client/connexion" class="btn btn-back">Retour</a>
                    <button type="submit" class="btn btn-next">Suivant</button>
            </div>

            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
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

        const dateLabel = document.querySelector('.date-label');
        const dateInputs = document.querySelectorAll('.date-inputs .form-control');
        const form = document.querySelector('form');
        const mdp = document.getElementById('mdp');
        const mdpConfirm = document.getElementById('mdp_confirm');
        const email = document.getElementById('email');
        const prenom = document.getElementById('prenom');
        const nom = document.getElementById('nom');
        const jour = document.getElementById('jour');
        const mois = document.getElementById('mois');
        const annee = document.getElementById('annee');

        let emailTouched = false;
        let mdpTouched = false;
        let mdpConfirmTouched = false;

        // Validation de l'email au blur (quand on quitte le champ)
        email.addEventListener('blur', function() {
            if (this.value) {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(this.value)) {
                    this.setCustomValidity('Veuillez entrer une adresse email valide');
                    document.getElementById('email-error').style.display = 'block';
                    this.classList.add('error');
                } else {
                    this.setCustomValidity('');
                    document.getElementById('email-error').style.display = 'none';
                    this.classList.remove('error');
                }
            }
        });

        // Retirer les messages d'erreur pendant la saisie de l'email
        email.addEventListener('input', function() {
            document.getElementById('email-error').style.display = 'none';
            document.getElementById('email-empty').style.display = 'none';
            this.classList.remove('error');
        });

        // Validation du mot de passe au blur (quand on quitte le champ)
        mdp.addEventListener('blur', function() {
            if (this.value) {
                const hasMinLength = this.value.length >= 8;
                const hasLower = /[a-z]/.test(this.value);
                const hasUpper = /[A-Z]/.test(this.value);
                const hasNumber = /\d/.test(this.value);
                const hasSpecial = /[@$!%*?&]/.test(this.value);
                
                if (!hasMinLength || !hasLower || !hasUpper || !hasNumber || !hasSpecial) {
                    this.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial');
                    document.getElementById('mdp-error').style.display = 'block';
                    this.classList.add('error');
                } else {
                    this.setCustomValidity('');
                    document.getElementById('mdp-error').style.display = 'none';
                    this.classList.remove('error');
                }
                // Vérifier la confirmation si elle a une valeur
                if (mdpConfirm.value) {
                    validatePasswordConfirmation();
                }
            }
        });

        // Retirer les messages d'erreur pendant la saisie
        mdp.addEventListener('input', function() {
            document.getElementById('mdp-error').style.display = 'none';
            document.getElementById('mdp-empty').style.display = 'none';
            this.classList.remove('error');
        });

        // Validation de la confirmation du mot de passe au blur
        mdpConfirm.addEventListener('blur', function() {
            if (this.value) {
                validatePasswordConfirmation();
            }
        });

        // Retirer les messages d'erreur pendant la saisie de la confirmation
        mdpConfirm.addEventListener('input', function() {
            document.getElementById('mdp-confirm-error').style.display = 'none';
            document.getElementById('mdp-confirm-empty').style.display = 'none';
            this.classList.remove('error');
        });

        function validatePasswordConfirmation() {
            if (mdp.value && mdpConfirm.value) {
                if (mdpConfirm.value !== mdp.value) {
                    mdpConfirm.setCustomValidity('Les mots de passe ne correspondent pas');
                    document.getElementById('mdp-confirm-error').style.display = 'block';
                    mdpConfirm.classList.add('error');
                } else {
                    mdpConfirm.setCustomValidity('');
                    document.getElementById('mdp-confirm-error').style.display = 'none';
                    mdpConfirm.classList.remove('error');
                }
            }
        }

        // Validation des champs de date pour n'accepter que des chiffres
        jour.addEventListener('input', function(e) {
            // Remplacer tout ce qui n'est pas un chiffre par une chaîne vide
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Validation de la plage de valeurs
            const value = parseInt(this.value);
            if (this.value && (value < 1 || value > 31)) {
                this.setCustomValidity('Le jour doit être compris entre 1 et 31');
                document.getElementById('jour-error').style.display = 'block';
                this.classList.add('error');
            } else {
                this.setCustomValidity('');
                document.getElementById('jour-error').style.display = 'none';
                this.classList.remove('error');
            }
        });

        annee.addEventListener('input', function(e) {
            // Remplacer tout ce qui n'est pas un chiffre par une chaîne vide
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Validation uniquement du nombre de chiffres
            if (this.value && this.value.length !== 4) {
                this.setCustomValidity('L\'année doit contenir 4 chiffres');
                this.classList.add('error');
            } else {
                this.setCustomValidity('');
                this.classList.remove('error');
            }
        });

        // Empêcher la saisie de lettres dans les champs numériques
        jour.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        annee.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Gestion des champs avec label flottant
        document.querySelectorAll('.form-control').forEach(input => {
            if (input.value) {
                input.classList.add('has-value');
            }

            input.addEventListener('input', function() {
                if (this.value) {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }
                checkDateFields();
            });

            if (input.tagName === 'SELECT') {
                input.addEventListener('change', function() {
                    if (this.value) {
                        this.classList.add('has-value');
                    } else {
                        this.classList.remove('has-value');
                    }
                    checkDateFields();
                });
            }
        });

        // Gestion du focus pour les champs de date
        dateInputs.forEach(input => {
            input.addEventListener('focus', () => {
                dateLabel.style.opacity = '0';
            });

            input.addEventListener('blur', () => {
                checkDateFields();
            });
        });

        function checkDateFields() {
            let hasValue = false;
            dateInputs.forEach(input => {
                if (input.value) {
                    hasValue = true;
                }
            });

            if (hasValue) {
                dateLabel.style.opacity = '0';
            } else {
                dateLabel.style.opacity = '1';
            }
        }

        // Validation du formulaire avant envoi
        form.addEventListener('submit', function(e) {
            let hasError = false;

            // Vérification de l'email
            if (!email.value) {
                hasError = true;
                email.classList.add('error');
                document.getElementById('email-empty').style.display = 'block';
            } else {
                document.getElementById('email-empty').style.display = 'none';
                if (!email.checkValidity()) {
                    hasError = true;
                    email.classList.add('error');
                    document.getElementById('email-error').style.display = 'block';
                } else {
                    document.getElementById('email-error').style.display = 'none';
                    email.classList.remove('error');
                }
            }

            // Vérification du mot de passe
            if (!mdp.value) {
                hasError = true;
                mdp.classList.add('error');
                document.getElementById('mdp-empty').style.display = 'block';
                document.getElementById('mdp-error').style.display = 'none';
            } else {
                document.getElementById('mdp-empty').style.display = 'none';
                if (!mdp.checkValidity()) {
                    hasError = true;
                    mdp.classList.add('error');
                    document.getElementById('mdp-error').style.display = 'block';
                } else {
                    document.getElementById('mdp-error').style.display = 'none';
                    mdp.classList.remove('error');
                }
            }

            // Vérification de la confirmation du mot de passe
            if (!mdpConfirm.value) {
                hasError = true;
                mdpConfirm.classList.add('error');
                document.getElementById('mdp-confirm-empty').style.display = 'block';
                document.getElementById('mdp-confirm-error').style.display = 'none';
            } else {
                document.getElementById('mdp-confirm-empty').style.display = 'none';
                if (mdpConfirm.value !== mdp.value) {
                    hasError = true;
                    mdpConfirm.classList.add('error');
                    document.getElementById('mdp-confirm-error').style.display = 'block';
                } else {
                    document.getElementById('mdp-confirm-error').style.display = 'none';
                    mdpConfirm.classList.remove('error');
                }
            }

            // Vérification du prénom
            if (!prenom.value) {
                hasError = true;
                prenom.classList.add('error');
                document.getElementById('prenom-empty').style.display = 'block';
            } else {
                document.getElementById('prenom-empty').style.display = 'none';
                prenom.classList.remove('error');
            }

            // Vérification du nom
            if (!nom.value) {
                hasError = true;
                nom.classList.add('error');
                document.getElementById('nom-empty').style.display = 'block';
            } else {
                document.getElementById('nom-empty').style.display = 'none';
                nom.classList.remove('error');
            }

            // Validation des champs de date
            if (!jour.value) {
                hasError = true;
                jour.classList.add('error');
                document.getElementById('jour-empty').style.display = 'block';
            } else {
                document.getElementById('jour-empty').style.display = 'none';
                const jourValue = parseInt(jour.value);
                if (jourValue < 1 || jourValue > 31) {
                    hasError = true;
                    jour.classList.add('error');
                    document.getElementById('jour-error').style.display = 'block';
                } else {
                    document.getElementById('jour-error').style.display = 'none';
                    jour.classList.remove('error');
                }
            }

            if (!mois.value) {
                hasError = true;
                mois.classList.add('error');
                document.getElementById('mois-empty').style.display = 'block';
            } else {
                document.getElementById('mois-empty').style.display = 'none';
                mois.classList.remove('error');
            }

            if (!annee.value) {
                hasError = true;
                annee.classList.add('error');
                document.getElementById('annee-empty').style.display = 'block';
            } else {
                document.getElementById('annee-empty').style.display = 'none';
                if (annee.value.length !== 4) {
                    hasError = true;
                    annee.classList.add('error');
                } else {
                    annee.classList.remove('error');
                }
            }

            if (hasError) {
                e.preventDefault();
            }
            });
        });
    </script>
</body>
</html> 