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
    libpq-dev \  # Voeg deze regel toe om PostgreSQL-clientbibliotheken te installeren
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_pgsql  # Voeg pdo_pgsql toe

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

# Controleer of de database beschikbaar is voordat de migraties worden uitgevoerd
# Voer de migraties uit en start Apache
ENTRYPOINT ["sh", "-c", "until pg_isready -h $DB_HOST -p 5432; do echo waiting for database; sleep 2; done; php artisan migrate --force; apache2-foreground"]
