# base img
FROM hyperf/hyperf:8.2-alpine-v3.18-swoole
MAINTAINER taoran<taoran1401@gmail.com>
WORKDIR /app
COPY . .
# install certbot
RUN apk update && apk add certbot
# composer install
RUN cd service && composer install