FROM alpine:3.9

# Install bash, runit, nginx and php7 (with all components)
RUN apk update \
    && apk add --no-cache \
    bash \
    runit \
    nginx \
    php7 \
    php7-apcu \
    php7-dom \
    php7-curl \
    php7-fpm \
    php7-gd \
    php7-iconv \
    php7-intl \
    php7-json \
    php7-mbstring \
    php7-mcrypt \
    php7-opcache \
    php7-openssl \
    php7-phar \
    php7-session \
    php7-zip \
    php7-zlib \
    # forward request and error logs to docker log collector
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Create directories
RUN mkdir -p /run/nginx \
    && mkdir -p /var/www/html \
    && mkdir -p /hd

# Add configurations and runit scripts
COPY conf/etc /etc

# copy in src
COPY src/ /var/www/html/

# Add Start Scripts
COPY scripts/start.sh /start.sh

# Allow executables
RUN chmod u+x /etc/service/nginx/run \
    && chmod u+x /etc/service/php-fpm/run \
    && chmod u+x /start.sh

# Optimize php configurations
RUN sed -i \
        -e "s/upload_max_filesize\s*=\s*2M/upload_max_filesize = 64M/g" \
        -e "s/post_max_size\s*=\s*8M/post_max_size = 64M/g" \
        -e "s/variables_order = \"GPCS\"/variables_order = \"EGPCS\"/g" \
        /etc/php7/php.ini && \
    sed -i \
        -e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" \
        -e "s/;pm.max_requests = 500/pm.max_requests = 500/g" \
        -e "s/user = nobody/user = nginx/g" \
        -e "s/group = nobody/group = nginx/g" \
        -e "s/^;clear_env = no$/clear_env = no/" \
        /etc/php7/php-fpm.d/www.conf && \
    sed -i \
        -e "s/;daemonize\s*=\s*yes/daemonize = no/g" \
        /etc/php7/php-fpm.conf

VOLUME /var/www/html

EXPOSE 80

CMD ["/start.sh"]
