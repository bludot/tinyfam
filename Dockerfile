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
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '795f976fe0ebd8b75f26a6dd68f78fd3453ce79f32ecb33e7fd087d39bfeb978342fb73ac986cd4f54edd0dc902601dc') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/local/bin
RUN php -r "unlink('composer-setup.php');"
RUN php /usr/local/bin/composer.phar install
RUN sed -i.bak 's/php:9000/localhost:9000/' /etc/nginx/conf.d/default.conf
RUN chown -R www-data:www-data /var/www
RUN chmod +x entrypoint.sh
CMD ["sh", "./entrypoint.sh"]