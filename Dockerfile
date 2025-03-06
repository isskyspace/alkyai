# Utiliser une image PHP avec Apache préinstallé
FROM php:8.2-apache

# Installer les extensions nécessaires (MySQL, mod_rewrite, etc.)
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

# Changer le port Apache pour Railway si nécessaire (par défaut 80)
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

# Copier les fichiers du backend dans le conteneur
COPY . /var/www/html/

# Définir le répertoire de travail
WORKDIR /var/www/html/

# Exposer le bon port pour Railway
EXPOSE 8080

# Lancer Apache en mode foreground
CMD ["apache2-foreground"]
