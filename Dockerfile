FROM php:8.2-apache

# Copie tous les fichiers PHP dans le dossier du serveur
COPY . /var/www/html/

# Expose le port 80
EXPOSE 80

# Démarre Apache avec PHP
CMD ["apache2-foreground"]
