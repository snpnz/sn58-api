FROM php:8.1.0


RUN apt-get update \
    && apt-get install -y curl wget \
    && apt-get install -y libonig-dev libpq-dev \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql

RUN a2enmod rewrite

# use your users $UID and $GID below
RUN groupadd apache-www-volume -g 1000
RUN useradd apache-www-volume -u 1000 -g 1000

ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

CMD ["apache2-foreground"]
