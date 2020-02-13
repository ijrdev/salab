FROM ubuntu:18.04
LABEL maintainer "ijrdev@gmail.com"

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
 && apt-get install -y curl apache2 \
 && apt-get install -y php libapache2-mod-php \
 && apt-get install -y php-mbstring php-intl php-mysql php-gmp php-bcmath\
 && a2enmod rewrite \
 && a2enmod headers \
 && ln -s /var/www/html /html

COPY docker/000-default.conf /etc/apache2/sites-available/
