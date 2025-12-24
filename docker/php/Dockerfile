FROM php:7.4-fpm-alpine

ENV TZ=Asia/Shanghai

# 运行时依赖需要保留，不能放进 .build-deps
RUN apk add --no-cache \
    git \
    unzip \
    tzdata \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    oniguruma \
  && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    oniguruma-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j"$(nproc)" \
    bcmath \
    pdo_mysql \
    mysqli \
    gd \
    zip \
    opcache \
    mbstring \
  && apk del .build-deps

# 为开发环境允许 php-fpm 以 root 用户运行
# 写入自定义 entrypoint 脚本，用 -R 参数启动 php-fpm
RUN sed -i 's/^user = .*/user = root/' /usr/local/etc/php-fpm.d/www.conf \
 && sed -i 's/^group = .*/group = root/' /usr/local/etc/php-fpm.d/www.conf \
 && { \
      echo '#!/bin/sh'; \
      echo 'exec php-fpm --allow-to-run-as-root'; \
    } > /usr/local/bin/docker-entrypoint-fpm-root.sh \
 && chmod +x /usr/local/bin/docker-entrypoint-fpm-root.sh

CMD ["/usr/local/bin/docker-entrypoint-fpm-root.sh"]

# 安装 Composer（给需要时用：composer install/update）
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/99-daikuan.ini

WORKDIR /var/www/html


