セットアップ方法
以下をターミナルで実行

cd hoge<br>
#### 手順1: コードを作業用ディレクトリーにダウンロードする。<br>
git clone https://github.com/kisapapa1227/retrek-ui.git<br>
cd retrek-ui<br>
git clone https://github.com/kisapapa1227/ReTReKpy.git<br>
cp .env.easy .env # docker の設定ファイルを準備する。<br>
\# oi

#### 手順2:Dockerコンテナを作成する。<br>
sudo su<br>
docker-compose down<br>
docker-compose rm -f<br>
docker volume prune -f<br>
docker network prune -f<br>
docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/var/www/html -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs<br>

#### https の設定（port:80 の利用状態の確認）<br>
sudo su<br>
lsos -i:80　# ポートの利用状態を確認する。<br>
COMMAND    PID     USER   FD   TYPE DEVICE SIZE/OFF NODE NAME<br>
apache2 113106     root    4u  IPv6 864638      0t0  TCP *:80 (LISTEN)<br>
<br>
上記のように表示された場合、TCPポート:80 を利用するので、すでに使われている場合はサービスを停止する。<br>
service apache2 stop<br>

同様に、<br>
/var/www/html がある場合、別名で保存しておく<br>
mv /var/www/html /var/www/html.org<br>

#### 手順3:Dockerコンテナの反映する。<br>
ln -s $(pwd) /var/www/html<br>
chmod 666 /var/www/html/.env<br>
touch /var/www/html/storage/logs/laravel.log<br>
chmod 666 /var/www/html/storage/logs/laravel.log<br>
chmod -R 777 /var/www/html<br>

./vendor/bin/sail up -d<br>
./vendor/bin/sail artisan key:generate<br>
./vendor/bin/sail artisan migrate<br>
./vendor/bin/sail npm install<br>
./vendor/bin/sail npm run build<br>

使用方法（以下のコマンドをターミナルで実行したのち、localhost80に接続して使用する）
./vendor/bin/sail up<br>
ブラウザで<br>
http://localhost<br>
に接続する
