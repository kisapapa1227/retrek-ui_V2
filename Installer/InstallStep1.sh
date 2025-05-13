#!/bin/sh
#
#
git clone https://github.com/kisapapa1227/retrek-ui.git
cd retrek-ui
git clone https://github.com/kisapapa1227/ReTReKpy.git
#.env
cat .env.example | grep -v DB > .env

echo >> .env
echo "DB_CONNECTION=mysql" >> .env
echo "DB_HOST=mysql" >> .env
echo "DB_PORT=3306" >> .env
echo "DB_DATABASE=retrek_ui" >> .env
echo "DB_USERNAME=sail" >> .env
echo "DB_PASSWORD=password" >> .env

echo >> .env
echo "WWWUSER=1000" >> .env
echo "WWWGROUP=1000" >> .env
