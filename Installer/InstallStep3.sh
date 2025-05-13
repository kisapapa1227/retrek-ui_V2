#!/bin/sh
#
#
if [ -e /var/www/html ] ; then
	sudo rm /var/www/html
fi
cd retrek-ui

sudo ln -s $(pwd) /var/www/html
#log ファイル、一時ファイル用の書き込みパーミッションをつける。
chmod 666 /var/www/html/.env
touch /var/www/html/storage/logs/laravel.log
chmod 666 /var/www/html/storage/logs/laravel.log
chmod -R 777 /var/www/html
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
