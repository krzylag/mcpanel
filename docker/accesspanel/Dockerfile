FROM php:8.3-fpm AS build-base

ARG TERM=linux
ARG DEBIAN_FRONTEND=noninteractive

# Install container tools
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    wget curl net-tools iproute2 supervisor graphviz unzip nginx vim git \
    ca-certificates curl gnupg2

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

COPY ./docker/accesspanel/php/php-fpm.d/* /usr/local/etc/php-fpm.d/
COPY ./docker/accesspanel/php/conf.d/* /usr/local/etc/php/conf.d/

COPY --from=composer/composer:2 /usr/bin/composer /usr/local/bin/composer

RUN install-php-extensions intl mysqli mbstring curl opcache apcu

# Install Symfony tool
RUN curl -sS https://get.symfony.com/cli/installer > /symfony-installer
RUN chmod +x /symfony-installer
RUN /symfony-installer --install-dir=/usr/local/sbin
RUN rm -rf /symfony-installer

# Configure nginx
COPY ./docker/accesspanel/nginx/nginx.conf /etc/nginx/nginx.conf
RUN rm -rf /etc/nginx/sites-enabled/*
RUN rm -rf /etc/nginx/sites-available/*
COPY ./docker/accesspanel/nginx/sites-available/default /etc/nginx/sites-available/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log

# Configure supervisor
COPY ./docker/accesspanel/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./docker/accesspanel/supervisor/conf.d/* /etc/supervisor/conf.d/

# Configure entrypoint scripts
RUN mkdir -p /entrypoint.d
COPY ./docker/accesspanel/entrypoint/entrypoint.d/* /entrypoint.d/
RUN chmod -R +x /entrypoint.d/*
COPY ./docker/accesspanel/entrypoint/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/html

RUN mkdir -p /var/www/html/var

CMD ["/entrypoint.sh", "/usr/bin/supervisord"]

FROM build-base AS build-dev

# Configure PHP
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN rm /usr/local/etc/php/php.ini-production

# Additional PHP modules for development
RUN install-php-extensions xdebug

# Additional entrypoint scripts for development
COPY ./docker/accesspanel/entrypoint/entrypoint.d-dev/* /entrypoint.d/

FROM build-base AS build-prod

# Configure PHP
RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
RUN rm /usr/local/etc/php/php.ini-development

# Build project
COPY ./* /var/www/html
RUN git config --global --add safe.directory /var/www/html
RUN composer install --no-interaction