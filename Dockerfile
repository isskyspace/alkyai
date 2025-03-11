# Utiliser l'image officielle PHP avec Apache
FROM php:7.4-apache

# Copier le code de votre application dans le répertoire approprié
COPY . /var/www/html/

# Exposer le port 80 pour permettre l'accès à l'application
EXPOSE 80
