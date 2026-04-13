FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=Europe/Brussels \
    PHP_VERSION=8.4

# -------------------------------------------------------------------
# Système + outils de base
# -------------------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates apt-transport-https software-properties-common \
    wget curl gnupg jq nano htop git unzip bzip2 locales acl \
    && ln -fs /usr/share/zoneinfo/$TZ /etc/localtime \
    && dpkg-reconfigure -f noninteractive tzdata

# -------------------------------------------------------------------
# Ajout du PPA PHP Ondrej (inclut PHP 8.4)
# -------------------------------------------------------------------
RUN add-apt-repository ppa:ondrej/php -y \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
    php${PHP_VERSION}-fpm php${PHP_VERSION}-common php${PHP_VERSION}-curl \
    php${PHP_VERSION}-mysql php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml \
    php${PHP_VERSION}-bcmath php${PHP_VERSION}-gd php${PHP_VERSION}-zip \
    php${PHP_VERSION}-intl php${PHP_VERSION}-sqlite3 php${PHP_VERSION}-imap \
    php${PHP_VERSION}-imagick php${PHP_VERSION}-odbc \
    && mkdir -p /var/run/php

# -------------------------------------------------------------------
# NGINX (repo officiel moderne)
# -------------------------------------------------------------------
RUN wget -qO- https://nginx.org/keys/nginx_signing.key | gpg --dearmor \
        -o /usr/share/keyrings/nginx.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/nginx.gpg] \
        http://nginx.org/packages/ubuntu `lsb_release -cs` nginx" \
        > /etc/apt/sources.list.d/nginx.list \
    && apt-get update \
    && apt-get install -y nginx

# Copie la configuration NGINX pour RetroHubConnect
COPY docker/retrohubconnect_nginx.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/log/retrohubconnect \
    && chown www-data:www-data /var/log/retrohubconnect

# -------------------------------------------------------------------
# Chrome stable
# -------------------------------------------------------------------
RUN wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | gpg --dearmor \
        -o /usr/share/keyrings/google.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/google.gpg] \
        http://dl.google.com/linux/chrome/deb/ stable main" \
        > /etc/apt/sources.list.d/google-chrome.list \
    && apt-get update \
    && apt-get install -y google-chrome-stable

# -------------------------------------------------------------------
# Workspace Laravel
# -------------------------------------------------------------------
RUN mkdir -p /www/retrohubconnect/files
WORKDIR /www/retrohubconnect
RUN ln -s /www/retrohubconnect/files /www/files \
    && git config --global --add safe.directory /www/retrohubconnect

COPY docker/php/www.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf


# -------------------------------------------------------------------
# Composer + PHPUnit
# -------------------------------------------------------------------
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin --filename=composer \
    && wget https://phar.phpunit.de/phpunit-10.phar \
    && chmod +x phpunit-10.phar \
    && mv phpunit-10.phar /usr/local/bin/phpunit

# Ajout de composer au PATH
ENV PATH="/root/.config/composer/vendor/bin:${PATH}"

# Installer Laravel Installer
RUN composer global require laravel/installer



# Crée les dossiers nécessaires
RUN mkdir -p /www/retrohubconnect/storage /www/retrohubconnect/bootstrap/cache

# Copie le code Laravel dans le conteneur
COPY --chown=www-data:www-data . /www/retrohubconnect

# Donne les permissions nécessaires
RUN chown -R www-data:www-data /www/retrohubconnect/storage /www/retrohubconnect/bootstrap/cache
RUN chmod -R 775 /www/retrohubconnect/storage /www/retrohubconnect/bootstrap/cache

# Configure PHP pour utiliser /tmp comme dossier temporaire
RUN { \
    echo 'upload_tmp_dir = /tmp'; \
    echo 'sys_temp_dir = /tmp'; \
    echo 'upload_max_filesize = 100M'; \
    echo 'post_max_size = 100M'; \
    } | tee -a /etc/php/8.4/fpm/conf.d/tempdir.ini

# Configure le dossier temporaire pour PHP
ENV TMPDIR=/tmp
RUN mkdir -p /tmp && chmod 1777 /tmp

# -------------------------------------------------------------------
# Entrypoint
# -------------------------------------------------------------------
RUN echo "\
#!/bin/sh\n\
service php${PHP_VERSION}-fpm start\n\
nginx -g 'daemon off;' &\n\
tail -s 1 /var/log/nginx/*.log -f\n\
" > /start.sh \
    && chmod +x /start.sh

EXPOSE 80
CMD ["sh", "/start.sh"]

# Nettoyage
RUN apt-get clean && rm -rf /var/lib/apt/lists/*