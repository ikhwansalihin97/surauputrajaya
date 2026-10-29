FROM php:8.5-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql gd zip intl bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Not `artisan serve`: it drops env vars that also exist in .env, which would
# discard the Docker overrides (DB_HOST, MAIL_HOST, ...) from docker-compose.yml.
ENV PHP_CLI_SERVER_WORKERS=4
CMD ["sh", "-c", "cd public && exec php -S 0.0.0.0:8003 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php"]
