#!/bin/sh

# Activa el modo de salida en error para detener el script si algo falla
set -e

# Iniciar PHP-FPM en segundo plano
php-fpm &

# Iniciar Nginx en primer plano.
# Esto es crucial, ya que el proceso en primer plano mantiene el contenedor vivo.
nginx -g 'daemon off;'