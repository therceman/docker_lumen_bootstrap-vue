FROM alpine:3.15

RUN apk --no-cache add \
bash \
php7 \
php7-fpm \
php7-pdo \
php7-mbstring \
php7-openssl \
php7-mysqli \
php7-pdo_mysql \
php7-json \
php7-dom \
curl \
php7-curl \
php7-tokenizer \
php7-phar \
php7-xml \
php7-xmlwriter

# composer
RUN php7 -r "copy('http://getcomposer.org/installer', 'composer-setup.php');" && \
php7 composer-setup.php --install-dir=/usr/bin --filename=composer && \
php7 -r "unlink('composer-setup.php');"

# copy all of the file in folder to /src
COPY . /src
WORKDIR /src

ARG ENV=dev

RUN if [[ "$ENV" = 'dev' ]] ; then echo 'Composer update will be executed later' ; else composer update ; fi