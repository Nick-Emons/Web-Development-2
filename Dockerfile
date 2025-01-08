# Gebruik een officiële PHP + Apache image
FROM php:8.1-apache

# Installeer benodigde extensies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_pgsql

# Installeer Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Stel de werkdirectory in
WORKDIR /var/www/html

# Kopieer projectbestanden
COPY . .

# Installeer afhankelijkheden via Composer
RUN composer install --no-dev --optimize-autoloader

# Stel permissies in voor storage en bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Zet de Apache DocumentRoot naar de public directory
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Voeg mod_rewrite toe en configureer de rewrite-regels
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        RewriteEngine On\n\
        # Sta directe toegang tot statische bestanden toe\n\
        RewriteCond %{REQUEST_FILENAME} -f [OR]\n\
        RewriteCond %{REQUEST_FILENAME} -d\n\
        RewriteRule ^ - [L]\n\
        # Omleidingen voor SPA-routes\n\
        RewriteRule ^ /index.html [L]\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Schakel mod_rewrite in
RUN a2enmod rewrite

# Expose de juiste poort
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
