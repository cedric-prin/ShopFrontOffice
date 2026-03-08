<?php
/**
 * Point d'entrée principal - Redirige vers public/
 * 
 * Si vous accédez à http://localhost/prin_boutique/
 * vous serez automatiquement redirigé vers http://localhost/prin_boutique/public/
 */

// Redirection vers le dossier public
header('Location: public/');
exit;

