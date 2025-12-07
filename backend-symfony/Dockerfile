FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git zip unzip libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copier uniquement composer.json et composer.lock d'abord pour le cache
COPY app/composer*.json ./

RUN composer install

# Copier le reste du projet
COPY app/. .

# Commande pour lancer Symfony
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

