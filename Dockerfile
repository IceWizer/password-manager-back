FROM php:8.2.0-fpm-alpine
LABEL authors="flori"

COPY --from=composer:2.1.9 /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache \
	bash \
	curl \
	git \
	libzip-dev \
	unzip \
	zip \
	&& docker-php-ext-install \
	pdo_mysql \
	zip \
	&& rm -rf /var/cache/apk/*

COPY ./ /var/www/html/

RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Run composer install to install the dependencies
RUN composer install --no-interaction

EXPOSE 80

# Entrypoint script
COPY ./docker/php/entrypoint.sh /var/www/html/docker/php/entrypoint.sh
RUN chmod +x /var/www/html/docker/php/entrypoint.sh

ADD docker/php/conf.d/php.ini /usr/local/etc/php/
ADD docker/php/conf.d/www.conf /usr/local/etc/php-fpm.d/

#ENTRYPOINT ["/var/www/html/docker/php/entrypoint.sh"]
