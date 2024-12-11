FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    gd

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем PhpSpreadsheet через Composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev

# Копируем файлы проекта
COPY . /var/www/html

# Устанавливаем права
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Настраиваем рабочую директорию
WORKDIR /var/www/html/public

# Указываем порт
EXPOSE 9000

# Запуск PHP-FPM
CMD ["php-fpm"]