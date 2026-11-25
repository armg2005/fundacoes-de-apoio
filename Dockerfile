FROM php:8.3-cli

# pdo_sqlite já vem na imagem oficial; unzip/git são usados pelo Composer.
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress --prefer-dist

COPY . .
RUN composer dump-autoload --optimize && mkdir -p /app/data

ENV DATABASE_PATH=/app/data/foundations.sqlite

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
