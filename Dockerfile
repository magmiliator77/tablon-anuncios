# #####################################################################
#  "Receta" de la imagen del contenedor web (igual que en el 2.7)
# #####################################################################

# Parto de la imagen oficial de PHP 8.3 que ya trae Apache
FROM php:8.3-apache

# pdo_mysql: el "driver" que necesita PDO para hablar con MySQL
RUN docker-php-ext-install pdo_mysql

# mod_rewrite: imprescindible para que funcione el .htaccess
RUN a2enmod rewrite

# Apache servirá la carpeta public/ (DocumentRoot), nunca la raíz:
# así nadie puede abrir src/, vendor/ o config.php desde el navegador.
# El último sed pone "AllowOverride All" para que Apache haga caso al .htaccess
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# Copio Composer para generar el autoloader al arrancar
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
