FROM php:8.4-fpm-alpine

RUN apk --no-cache add \
    git \
    curl \
    zip \
    libxml2-dev \
    libzip-dev \
    zlib-dev \
    linux-headers \
    oniguruma-dev \
    icu-dev \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    autoconf \
    make \
    gcc \
    g++ \
    su-exec

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install \
          pdo_mysql \
          mbstring \
          mysqli \
          exif \
          pcntl \
          bcmath \
          gd \
          zip \
          opcache \
          sockets \
          intl \
          xml \
          pdo_pgsql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# переменная окружения для Xdebug
ENV PHP_IDE_CONFIG 'serverName=app-dev'

COPY ./xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www

COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
