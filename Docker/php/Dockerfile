FROM php:7.4-fpm

# Install necessary extensions
RUN docker-php-ext-install pdo pdo_mysql

# install npm
RUN apt-get update && apt-get install -y npm

# install libraries in package.json
WORKDIR /var/www/html
RUN npm install
