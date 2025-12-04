FROM php:8.2-apache

# Installation des extensions nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Active mod_rewrite pour MVC et mod_headers pour les types MIME
RUN a2enmod rewrite headers

# Copie du code dans le container
COPY . /var/www/html

# Vérification CRITIQUE que les fichiers statiques sont bien copiés
RUN echo "=== VÉRIFICATION CSS ===" && \
    if [ -d /var/www/html/public/css/vendor ]; then \
        echo "Répertoire CSS vendor trouvé:" && \
        ls -lah /var/www/html/public/css/vendor/ | head -10; \
    else \
        echo "ERREUR: Répertoire CSS vendor NON TROUVÉ"; \
    fi && \
    echo "=== VÉRIFICATION JS ===" && \
    if [ -d /var/www/html/public/js/vendor ]; then \
        echo "Répertoire JS vendor trouvé:" && \
        ls -lah /var/www/html/public/js/vendor/ | head -10; \
    else \
        echo "ERREUR: Répertoire JS vendor NON TROUVÉ"; \
    fi && \
    echo "=== VÉRIFICATION FICHIERS SPÉCIFIQUES ===" && \
    test -f /var/www/html/public/css/vendor/bootstrap.min.css && echo "✓ bootstrap.min.css OK" || echo "✗ bootstrap.min.css MANQUANT" && \
    test -f /var/www/html/public/js/vendor/bootstrap.bundle.min.js && echo "✓ bootstrap.bundle.min.js OK" || echo "✗ bootstrap.bundle.min.js MANQUANT"

# Installation des dépendances Composer (si composer.json existe)
WORKDIR /var/www/html
RUN if [ -f composer.json ]; then \
        composer install --no-dev --optimize-autoloader --no-interaction || echo "Composer install failed, but continuing..."; \
    else \
        echo "No composer.json found, skipping Composer install"; \
    fi

# Donne les droits
RUN chown -R www-data:www-data /var/www/html

# Configure le VirtualHost si besoin
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf