FROM php:7.4-fpm

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update \
    && apt-get install -yq zlib1g-dev libzip-dev unzip \
    && docker-php-ext-install zip

RUN docker-php-ext-install pdo_mysql bcmath pcntl

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN composer config -g repos.packagist composer https://packagist.kawax.biz/
RUN composer global require hirak/prestissimo

# aws cli
RUN apt-get install -yq --fix-missing python \
    && curl "https://bootstrap.pypa.io/get-pip.py"| python \
    && pip install awscli


# supervisor
RUN apt-get install -yq --fix-missing supervisor

COPY supervisor/supervisord.conf /etc/supervisor/
COPY supervisor/conf/ /etc/supervisor/conf.d/


# cron
RUN apt-get install -yq --fix-missing cron
COPY scheduler/root /var/spool/cron/crontabs/
RUN chmod 0600 /var/spool/cron/crontabs/root


# clean
RUN apt-get -yq upgrade
RUN apt-get -yq autoremove
RUN apt-get -yq clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


WORKDIR /var/www
