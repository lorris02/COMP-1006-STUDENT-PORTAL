FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql \
    && printf 'expose_php=Off\n' > /usr/local/etc/php/conf.d/security.ini \
    && printf 'DirectoryIndex view.php index.php\n' > /etc/apache2/conf-available/portal.conf \
    && a2enconf portal
COPY Php_files/Assignment_1/ /var/www/html/
COPY docker/start.sh /usr/local/bin/start-portal
ENV APP_MODE=demo PORT=80
EXPOSE 80
CMD ["sh", "/usr/local/bin/start-portal"]
