# FROM イメージ名:タグ名
FROM nginx:1.20-alpine

# ENVはコンテナ内のサーバー環境変数
ENV TZ Asia/Tokyo

RUN chmod 755 -R /root

# COPY [ローカル側のパス] [コンテナ側のパス]
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
