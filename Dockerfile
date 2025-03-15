# Utiliser l'image officielle PHP avec Apache
FROM php:7.4-apache

# Mettre à jour les paquets et installer les dépendances nécessaires
RUN apt-get update && apt-get install -y libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql pgsql
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Activer le module Apache rewrite (si nécessaire)
RUN a2enmod rewrite

# Copier le code de votre application dans le répertoire approprié
COPY . /var/www/html/

# Définir les permissions pour le répertoire Apache
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80 pour permettre l'accès à l'application
EXPOSE 80
