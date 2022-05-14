FROM php:8-alpine

RUN apk update && apk add zip unzip
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

RUN apk add --no-cache --update --virtual buildDeps autoconf gcc make g++ zlib-dev libressl-dev curl-dev openssl-dev

RUN pecl install mongodb
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN echo "extension=mongodb.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`

WORKDIR /app
