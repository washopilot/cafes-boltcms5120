FROM php:7.2-apache-buster AS php

# Install system dependencies and PHP extensions
RUN apt-get update && \
	apt-get install -y \
	zip \
	unzip \
	curl \
	libicu-dev \
	libbz2-dev \
	libpng-dev \
	libjpeg-dev \
	libreadline-dev \
	libfreetype6-dev \
	libxml2-dev \
	libzip-dev \
	g++ && \
	docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
	docker-php-ext-install -j$(nproc) \
	bz2 \
	intl \
	iconv \
	bcmath \
	opcache \
	calendar \
	exif \
	gd \
	mbstring \
	pdo_mysql \
	zip \
	xml && \
	pecl install apcu && \
	docker-php-ext-enable apcu && \
	apt-get clean && \
	rm -rf /var/lib/apt/lists/* /tmp/*

# Configure Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
	sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
	a2enmod rewrite headers

# Configure PHP
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/php/bolt.ini /usr/local/etc/php/conf.d/bolt.ini

# RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
# 	apt-get install -y nodejs && \
# 	apt-get clean && \
# 	rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create dev user
ARG uid=1000
ARG gid=1000
RUN groupadd -g $gid devuser && \
	useradd -u $uid -g $gid -G www-data,root -d /home/devuser devuser && \
	mkdir -p /home/devuser/.composer && \
	chown -R devuser:devuser /home/devuser

USER devuser

CMD \
	rm -rf var/cache && rm -rf var/log && rm -rf public/thumbs/* && \
	apache2-foreground
