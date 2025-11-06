FROM php:8.1-cli-alpine

WORKDIR /app


COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache --update \
    bash \
    git \
    sudo \
    openssh \
    autoconf \
    g++ \
    make \
    libxml2-dev \
    postgresql-libs \
    postgresql-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    curl-dev \
    zlib-dev \
    libevent-dev \
    icu-dev \
    nodejs \
    npm

RUN install-php-extensions \
    bcmath \
    exif \
    gd \
    intl \
    mbstring \
    opcache \
    pcntl \
    pdo_mysql \
    soap \
    sockets \
    zip \
    apcu \
    pcov \
    swoole \
    http

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer


COPY files/php/php.ini /usr/local/etc/php/conf.d/php.ini


RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ --allow-untrusted gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

ENV NODE_PATH "/home/www-data/.npm-global/lib/node_modules"
RUN mkdir "/home/www-data/.npm-global/" && \
    npm config set prefix "/home/www-data/.npm-global/" && \
    npm install -g chokidar

ENV SWOOLE_MAX_REQUESTS "500"
ENV SWOOLE_TASK_WORKERS "auto"
ENV SWOOLE_WATCH $true
ENV SWOOLE_WORKERS "auto"

RUN chown -R www-data:www-data /app

COPY ./start.sh /
RUN chmod +x /start.sh
ENTRYPOINT ["/start.sh"]