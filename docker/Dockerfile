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

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем только composer.json и composer.lock для установки зависимостей
COPY ../composer.json ../composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Копируем все остальные файлы проекта
COPY ../ ./

# Устанавливаем права
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Настраиваем рабочую директорию для PHP
WORKDIR /var/www/html/public

# Указываем порт
EXPOSE 9000

# Запуск PHP-FPM
CMD ["php-fpm"]
