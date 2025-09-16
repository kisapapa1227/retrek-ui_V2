#!/bin/sh
#
#

log=sql.log
if [ -e $log ]; then
	rm -f $log
fi

id=$(docker ps -a | grep mysql | awk '{print $1}')

#docker exec -it $id sh /var/mysqlTools/mysqlCtl.sh 4

docker exec -it $id sh /var/mysqlTools/mysqlCtl.sh 1

p1=$(cat $log | grep '%' | grep root)
if [ -z "$p1" ]; then
	docker exec -it $id sh /var/mysqlTools/mysqlCtl.sh 2
fi

p1=$(cat $log | grep '%' | grep sail)
if [ -z "$p1" ]; then
	docker exec -it $id sh /var/mysqlTools/mysqlCtl.sh 3
fi
