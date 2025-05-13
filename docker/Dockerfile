FROM php:7.2-apache-buster AS php

# 1. development packages
RUN apt-get update && \
	apt-get install -y \
	zip \
	curl \
	unzip \
	libicu-dev \
	libbz2-dev \
	libpng-dev \
	libjpeg-dev \
	libmcrypt-dev \
	libreadline-dev \
	libfreetype6-dev \
	g++ \
	libxml2-dev \
	libzip-dev && \
	apt-get clean && \
	rm -rf /var/lib/apt/lists/*

# 2. apache configs + document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
	sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers

# 4. start with base php config, then add extensions
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/php/bolt.ini /usr/local/etc/php/conf.d/bolt.ini

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
	docker-php-ext-install \
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
	rm -rf /tmp/*

# RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
# 	apt-get install -y nodejs && \
# 	apt-get clean && \
# 	rm -rf /var/lib/apt/lists/*

# 5. composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. we need a user with the same UID/GID with host user
# so when we execute CLI commands, all the host file's ownership remains intact
# otherwise command from inside container will create root-owned files and directories
# Define default arguments for UID and GID
ARG uid
ARG gid

# Create a new group with the specified GID
RUN groupadd -g $gid devuser

# Create a user and assign the UID and GID
RUN useradd -G www-data,root,devuser -u $uid -g $gid -d /home/devuser devuser && \
	mkdir -p /home/devuser/.composer && \
	chown -R devuser:devuser /home/devuser

# Set devuser as the default user when running the container
USER devuser

CMD \
	# npm ci -d && npm run build && \
	# rm -rf vendor && rm -rf public/assets && rm -rf public/bundles && \
	rm -rf var/cache && rm -rf var/log && rm -rf public/thumbs/* && \
	# composer install --prefer-dist --no-progress --no-suggest --ignore-platform-reqs && \
	# composer clear-cache && \
	apache2-foreground
