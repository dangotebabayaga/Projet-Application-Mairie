FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git zip unzip libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql opcache

# Activation de OPcache pour accélérer drastiquement les requêtes
# (cache les fichiers PHP compilés en RAM au lieu de re-parser à chaque requête)
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'realpath_cache_size=4096K'; \
    echo 'realpath_cache_ttl=600'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

ENV JWT_SECRET=0fb565046be8e509dbb1789b15881d55e195e24f0310e1667e0fd6bc2920ef4a
# Copier uniquement composer.json et composer.lock d'abord pour le cache
COPY app/composer*.json ./
# Optimize l'autoloader pour générer un classmap (plus de scan filesystem)
RUN composer install --optimize-autoloader
# Copier le reste du projet
COPY app/. .

# Commande pour lancer Symfony
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
