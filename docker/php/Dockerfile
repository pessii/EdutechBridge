# FROM イメージ名:タグ名
FROM php:8.0-fpm

# ENVはコンテナ内のサーバー環境変数
ENV TZ Asia/Tokyo

# RUN にはコンテナビルド時に実行するコマンドを書く
# && で複数のコマンドをつなぎ
# \  で改行
    # apt-get update：インストール可能なパッケージの「一覧」を更新
    # apt-get -y install：パッケージをインストール
    # docker-php-ext-install：PHPの拡張ライブラリをインストール
RUN apt-get update && \
	apt-get install -y git unzip libzip-dev libicu-dev libonig-dev && \
	docker-php-ext-install intl pdo_mysql zip bcmath

# COPY [ローカル側のパス] [コンテナ側のパス]
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Laravelを使うためComposerをインストール
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

# Node.jsをインストール
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get install -y nodejs

# $ docker-compose exec コンテナ名 bash というコマンドを実行する時のコンテナ名
WORKDIR /php
