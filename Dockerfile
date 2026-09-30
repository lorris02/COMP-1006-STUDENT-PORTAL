FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql
COPY Php_files/Assignment_1/ /var/www/html/
ENV APP_MODE=demo
EXPOSE 80
