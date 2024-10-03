#!/bin/sh
#
#
id=$(docker ps -a | grep sail | awk '{print $1}')
docker exec -it $id bash
