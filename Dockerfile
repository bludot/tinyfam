FROM harbor.floret.dev/tinyfam/build:1


WORKDIR /var/www
COPY application application
COPY config config
COPY Core Core
COPY db db
COPY public public
COPY composer.json composer.json
COPY composer.lock composer.lock
COPY Bootstrap.php Bootstrap.php
COPY index.php index.php
COPY php.ini $PHP_INI_DIR/php.ini
COPY conf.d /etc/nginx/conf.d
COPY entrypoint.sh entrypoint.sh

# Install and run Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin
RUN php -r "unlink('composer-setup.php');"
RUN php /usr/local/bin/composer.phar install
RUN sed -i.bak 's/php:9000/localhost:9000/' /etc/nginx/conf.d/default.conf
RUN touch .env
RUN chown -R www-data:www-data /var/www
RUN chmod +x entrypoint.sh
CMD ["sh", "./entrypoint.sh"]