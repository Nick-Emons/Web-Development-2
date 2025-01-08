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
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

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

# Expose de juiste poort
EXPOSE 80

# Stel de startcommand in
CMD ["apache2-foreground"]
