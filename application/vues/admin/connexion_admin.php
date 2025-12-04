<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="<?php echo asset_path('images/favicon.svg'); ?>" type="image/x-icon" />
  <title>Connexion Admin - Prin Boutique</title>

  <link rel="stylesheet" href="<?php echo asset_path('css/vendor/bootstrap.min.css'); ?>" />
  <link rel="stylesheet" href="<?php echo asset_path('css/vendor/lineicons.css'); ?>" />
  <link rel="stylesheet" href="<?php echo asset_path('css/vendor/materialdesignicons.min.css'); ?>" />
  <link rel="stylesheet" href="<?php echo asset_path('css/vendor/fullcalendar.css'); ?>" />
  <link rel="stylesheet" href="<?php echo asset_path('css/vendor/main.css'); ?>" />
</head>

<body>
  <div id="preloader">
    <div class="spinner"></div>
  </div>
  <div class="overlay"></div>
  <main class="main-wrapper">
    <section class="signin-section">
      <div class="container-fluid">
        <div class="title-wrapper pt-10">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="title">
                <h2>Se connecter</h2>
              </div>
            </div>
          </div>
        </div>
        <div class="row g-0 auth-row">
        </div>
        <div class="col-lg-6">
          <div class="signup-wrapper">
            <div class="form-wrapper">
              <h6 class="mb-15">Partie Administrateur</h6>
              <p class="text-sm mb-25">
                Se connecter en tant qu'admin.
              </p>

              <form method='post' action="index.php?controleur=Admin&action=verifierConnexion">
                <div class="row">
                  <div class="col-12">
                    <div class="input-style-1">
                      <label for="login">Nom d'utilisateur</label>
                      <input type="text" placeholder="Nom d'utilisateur" name='login' id='login' />
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="input-style-1">
                      <label for="passe">Mot de passe</label>
                      <input type="password" placeholder="Mot de passe" name='passe' id='passe'/>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-check checkbox-style mb-30">
                      <input class="form-check-input" type="checkbox" id="checkbox-not-robot" />
                      <label class="form-check-label" for="checkbox-not-robot">
                        Je ne suis pas un robot</label>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-check checkbox-style mb-30">
                      <input class="form-check-input" type="checkbox" id="connexion_auto" name="connexion_auto" />
                      <label class="form-check-label" for="connexion_auto">
                        Connexion automatique (rester connecté)
                      </label>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="button-group d-flex justify-content-center flex-wrap">
                        <button type='submit' class="main-btn primary-btn btn-hover w-100 text-center" value='Connexion'>
                        S'identifier
                       </button>
                    </div>
                  </div>
                </div>
              </form>
              
            </div>
          </div>
        </div>
      </div>
      </div>
    </section>
  </main>
  <script src="<?php echo asset_path('js/vendor/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo asset_path('js/vendor/Chart.min.js'); ?>"></script>
  <script src="<?php echo asset_path('js/pages/dynamic-pie-chart.js'); ?>"></script>
  <script src="<?php echo asset_path('js/vendor/moment.min.js'); ?>"></script>
  <script src="<?php echo asset_path('js/vendor/fullcalendar.js'); ?>"></script>
  <script src="<?php echo asset_path('js/vendor/jvectormap.min.js'); ?>"></script>
  <script src="<?php echo asset_path('js/vendor/world-merc.js'); ?>"></script>
  <script src="<?php echo asset_path('js/vendor/polyfill.js'); ?>"></script>
  <script>
    // Masquer le preloader après le chargement de la page
    window.addEventListener('load', function() {
      const preloader = document.getElementById('preloader');
      if (preloader) {
        preloader.style.display = 'none';
      }
    });
    // Fallback au cas où l'événement load ne se déclenche pas
    setTimeout(function() {
      const preloader = document.getElementById('preloader');
      if (preloader) {
        preloader.style.display = 'none';
      }
    }, 1000);
  </script>
</body>
</html>