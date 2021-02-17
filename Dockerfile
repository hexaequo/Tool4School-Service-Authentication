FROM php:8.0-fpm-alpine

# APK Installation
RUN apk add --no-cache bash

# Installing rabbitmq and postgresql
RUN set -ex \
  && apk --no-cache add \
    postgresql-dev \
    rabbitmq-c-dev

# Installing xdebug for code coverage
RUN apk add --no-cache $PHPIZE_DEPS \
    && docker-php-ext-install sockets \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Installing php ampq extension
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions
RUN install-php-extensions amqp

# Installing php pdo & pdo_pgsql extension
RUN docker-php-ext-install pdo pdo_pgsql

# Set workdir for following commands
WORKDIR /var/www

# Installing composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installing symfony
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Copy required file for run
COPY docker/wait.sh /usr/local/bin/wait.sh
COPY docker/init.sh /usr/local/bin/init.sh
COPY composer.json composer.lock ./

# Give execution rights to previously copied scripts
RUN chmod +x /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/wait.sh

# Installing composer dependencies
RUN composer install

# Generating rsa keys
RUN mkdir config/jwt/

RUN openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:testpassword
RUN openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:testpassword

# Modify original command
CMD bash -c "init.sh" \
    && php-fpm
