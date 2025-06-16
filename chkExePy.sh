#!/bin/sh
#
#
chk=$(ps aux | grep sail | grep exe.py)

while true ; do
if [ "$chk" = "" ] ; then
	echo end
	exit
fi
echo -n .
#echo $chk
done
