FROM php:7.4-apache

RUN apt-get update && apt-get install -y wget git unzip \
    && pecl install xdebug-2.9.2 \
    && docker-php-ext-enable xdebug

ADD ./php.ini /usr/local/etc/php/php.ini

RUN wget https://getcomposer.org/installer -O - -q \
    | php -- --install-dir=/bin --filename=composer --quiet

RUN apt-get update && apt-get install --no-install-recommends -y \
    libzip-dev \
    zlibc \
    zlib1g \
    && docker-php-ext-configure zip  \
    && docker-php-ext-install zip

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libmemcached-dev \
    curl \
    git

RUN echo 'xdebug.remote_port=9000' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.remote_connect_back=1' >> /usr/local/etc/php/php.ini

# Install package dependencies
RUN apt update && apt install -y libmagickwand-dev git libzip-dev unzip libgmp-dev

# Enable default PHP extensions
RUN docker-php-ext-install mysqli pdo_mysql pcntl bcmath zip soap intl gd exif gmp

COPY /vhosts/default-vhost.conf /etc/apache2/sites-available/
RUN a2dissite 000-default.conf
RUN a2ensite default-vhost.conf
RUN a2enmod rewrite

WORKDIR /var/www/html