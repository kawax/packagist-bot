FROM chilio/laravel-dusk-ci:stable

RUN cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

RUN apt install -y python
RUN curl "https://bootstrap.pypa.io/get-pip.py"| python
RUN pip install awscli

RUN composer config -g repos.packagist composer https://packagist.kawax.biz/
RUN composer global require hirak/prestissimo

COPY ./cron/scheduler.sh /usr/bin/scheduler.sh
RUN chmod +x /usr/bin/scheduler.sh

COPY ./supervisor /etc/supervisor/conf.d

WORKDIR /var/www
