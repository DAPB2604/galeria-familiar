FROM php:8.2-cli

WORKDIR /app

COPY composer.json ./

RUN apt-get update && apt-get install -y unzip curl git \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

COPY . .

# Puerto 80 para el servidor PHP embebido
EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "/app", "index.php"]
