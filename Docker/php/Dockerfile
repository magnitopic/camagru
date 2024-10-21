FROM php:7.4-fpm

# Install necessary libraries and extensions
RUN apt-get update && apt-get install -y \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	zlib1g-dev \
	libzip-dev \
	ssmtp\
	npm

ARG SSMTP_EMAIL
ARG SSMTP_AUTH_PASS

ENV SSMTP_EMAIL=$SSMTP_EMAIL
ENV SSMTP_AUTH_PASS=$SSMTP_AUTH_PASS

COPY ssmtp_config.sh /usr/local/bin/ssmtp_config.sh
RUN chmod +x /usr/local/bin/ssmtp_config.sh
RUN /usr/local/bin/ssmtp_config.sh

RUN chown root:mail /etc/ssmtp/ssmtp.conf
RUN chmod 640 /etc/ssmtp/ssmtp.conf

RUN echo "sendmail_path = /usr/sbin/ssmtp -t" > /usr/local/etc/php/conf.d/mail.ini

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd \
	&& docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www/html

# Create and set permissions for the uploads directory
RUN mkdir -p uploads && chmod 755 uploads

# Install npm dependencies
RUN npm install
