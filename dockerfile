FROM php:7.4-fpm-alpine3.16

ARG COMMIT_HASH
ENV TZ=America/Sao_Paulo

WORKDIR /var/www/html

# Install packages and remove default server definition
RUN apk --no-cache add \
  tzdata \
  gettext-dev \
  zlib-dev \
  libpng-dev \
  jpeg-dev \
  libzip-dev \
  freetype-dev \
  nginx \
  supervisor \
  curl \
  redis


RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Crontab file to the cron.d directory
# COPY crontab.txt /etc/cron.d/crontab-file

# RUN crontab /etc/cron.d/crontab-file && \
#   touch /var/log/cron.log

# RUN rm /etc/nginx/conf.d/default.conf
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip gettext mysqli pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Configure nginx
COPY docker-config/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY docker-config/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY docker-config/php.ini /usr/local/etc/php/php.ini

# Configure supervisord
COPY docker-config/supervisord.conf /etc/supervisord.conf

# Setup document root
RUN mkdir -p /var/www/html

# Install PHP dependencies
COPY composer.json /var/www/html/
RUN composer install --no-scripts --no-autoloader && \
    composer dump-autoload && \
    rm -rf /root/.composer

# Switch to use a non-root user from here on
COPY . /var/www/html/

#RUN chmod 777 -R /var/www/html/adm/push

RUN mkdir /lib64 && ln -s /lib/libc.musl-x86_64.so.1 /lib64/ld-linux-x86-64.so.2

# Expose the port nginx is reachable on
EXPOSE 8080  
# Let supervisord start nginx & php-fpm
CMD ["supervisord"]
