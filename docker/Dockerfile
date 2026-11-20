FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-enabled/livraria.conf \
 && printf 'upload_max_filesize=5M\npost_max_size=8M\ndate.timezone=America/Sao_Paulo\n' > /usr/local/etc/php/conf.d/livraria.ini
