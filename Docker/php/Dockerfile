FROM php:7.4-fpm

# Install necessary libraries and extensions
RUN apt-get update && apt-get install -y \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	zlib1g-dev \
	libzip-dev \
	npm


RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd \
	&& docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www/html

# Create and set permissions for the uploads directory
RUN mkdir -p uploads && chmod 755 uploads

# Install npm dependencies
RUN npm install