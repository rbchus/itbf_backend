# Imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias de PHP y herramientas
RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev zip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto al contenedor
COPY . .

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Dar permisos a las carpetas de Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Puerto por defecto de Apache
EXPOSE 80

# Comando para iniciar Apache
