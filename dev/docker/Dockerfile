FROM php:7.4-cli


### Install PHP extensions to match production hosting environment
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
# Run `docker run --rm php:7.4-cli php -m` to get the list of extensions installed by
# defaults. Compared to hosting environment PHP info, we may need these :
RUN install-php-extensions apcu mysqli exif gd imagick calendar gettext intl zip xmlrpc
# But not bcmath bz2 dba gmp imap ldap mailparse mongodb pdo_mysql soap sockets tidy xsl


### Install Composer dependencies and get the binary
RUN apt-get update \
    && apt-get install -y git unzip mercurial \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer /usr/bin/composer /usr/bin/composer


### Run Docker with a user similar to host's one (instead of root)
# A 'runner' user with the same UID/GID is created inside the container
ARG USER_ID
ARG GROUP_ID
RUN addgroup --gid $GROUP_ID runner \
    && adduser --disabled-password --gecos 'PHP Runner' --uid $USER_ID --gid $GROUP_ID runner
USER runner

EXPOSE 8080
