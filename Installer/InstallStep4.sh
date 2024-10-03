#!/bin/sh
#
#

docker ps
docker stop `docker ps -a -q`
docker rm `docker ps -a -q`

docker images -aq | xargs docker rmi
docker-compose down --rmi all --volumes --remove-orphans

docker ps
