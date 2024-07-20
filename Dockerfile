FROM php:8-apache

EXPOSE 80

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd zip intl xsl exif gettext pcntl sockets @composer

RUN sed -ri -e 's!/var/www/html!/var/www/html/public/!g' /etc/apache2/sites-available/*.conf

RUN sed -i '\!</VirtualHost>!i \        <Directory /var/www/html>\n                Options Indexes FollowSymLinks\n                AllowOverride All\n                Require all granted\n        </Directory>' /etc/apache2/sites-available/*.conf

RUN a2enmod rewrite

RUN curl -SL https://github.com/Gamerboy59/rpxy-webui/releases/latest/download/rpxy-webui.tar.gz | tar -xzC /var/www/html/

RUN chown -R www-data:www-data /var/www/html/

RUN cd /var/www/html/ && php artisan key:generate

RUN cd /var/www/html/ && php artisan migrate --seed
