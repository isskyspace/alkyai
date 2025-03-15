# Utiliser l'image officielle PHP avec Apache
FROM php:7.4-apache

# Mettre à jour les paquets et installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \          # Bibliothèque nécessaire pour PostgreSQL
    && docker-php-ext-install pdo pdo_pgsql pgsql \  # Installer les extensions PHP pour PostgreSQL
    && apt-get clean \   # Nettoyer le cache des paquets pour réduire la taille de l'image
    && rm -rf /var/lib/apt/lists/*

# Copier le code de votre application dans le répertoire approprié
COPY . /var/www/html/

# Exposer le port 80 pour permettre l'accès à l'application
EXPOSE 80
