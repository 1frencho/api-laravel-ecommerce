# =================================================================
# STAGE 1: Build (Construcción)
# Aquí instalamos dependencias y construimos los assets.
# =================================================================
FROM php:8.3-fpm-alpine AS build

# Instalar dependencias del sistema y extensiones de PHP necesarias para la construcción
RUN apk add --no-cache \
    zip \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    nodejs \
    npm \
    zlib-dev # Agregado aquí para consistencia

# Instalar extensiones de PHP
RUN docker-php-ext-install zip pdo pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Instalar Composer
COPY --from=composer:2.7.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar los archivos de la aplicación
COPY . .

# Instalar dependencias de PHP y Node.js, y construir los assets
RUN composer install --no-interaction --optimize-autologader --no-dev \
    && npm install \
    && npm run build

# Limpiar caché de npm
RUN npm cache clean --force

# =================================================================
# STAGE 2: Production (Producción)
# Esta es la imagen final, más ligera y optimizada.
# =================================================================
FROM php:8.3-fpm-alpine

# Instalar solo las dependencias de sistema necesarias para ejecutar la app
RUN apk add --no-cache \
    nginx \
    libzip \
    libjpeg-turbo \
    libpng \
    freetype \
    oniguruma \
    gettext \
    zlib-dev # <<<--- ¡LA LÍNEA CLAVE QUE FALTABA!

# Instalar extensiones de PHP para producción
RUN docker-php-ext-install \
    bcmath \
    exif \
    gd \
    gettext \
    opcache \
    pdo_mysql \
    zip

WORKDIR /var/www/html

# Copiar los archivos construidos desde el stage anterior
COPY --from=build /var/www/html .

# Copiar las configuraciones de Nginx y PHP
# Asegúrate de que estos archivos existan en la misma carpeta que tu Dockerfile
COPY ./nginx.conf /etc/nginx/http.d/default.conf
COPY ./php.ini "$PHP_INI_DIR/conf.d/app.ini"

# Copiar el script de inicio y darle permisos
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Ajustar permisos para que Laravel pueda escribir en estas carpetas
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto 80
EXPOSE 80

# Usar el script de inicio para arrancar el contenedor
ENTRYPOINT ["entrypoint.sh"]