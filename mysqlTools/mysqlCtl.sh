#!/bin/sh
#
#
ask=/var/mysqlTools/askUser.sql
out=/var/mysqlTools/sql.log

if [ $1 -eq 1 ]; then
	mysql --password=password < $ask > $out
elif [ $1 == 2 ]; then
	ask=/var/mysqlTools/addRoot.sql
	mysql --password=password < $ask >> $out
elif [ $1 == 3 ]; then
	ask=/var/mysqlTools/addSail.sql
	mysql --password=password < $ask >> $out
elif [ $1 == 4 ]; then
	ask=/var/mysqlTools/dropRoot.sql
	mysql --password=password < $ask >> $out
fi
