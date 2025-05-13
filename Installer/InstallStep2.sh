#!/bin/sh
#
#
cd retrek-ui
sudo docker-compose down
sudo docker-compose rm -f
sudo docker volume prune -f
sudo docker network prune -f
sudo docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/var/www/html -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs
