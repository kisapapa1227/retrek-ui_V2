#!/bin/sh
#
#
chk=$(ps aux | grep sail | grep exe.py)

if [ "$chk" = "" ] ; then
	exit
fi

iid=$(echo $chk | awk '{print $2}')
echo $chk
kill -9 $iid
