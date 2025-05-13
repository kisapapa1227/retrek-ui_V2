# retrek-ui : user interface for ReTReK
このパッケージはReTReK (逆合成の知識を使用したデータ駆動型コンピュータ支援合成計画ツール)をユーザーフレンドリーに使うためのウェブインタフェースを提供する。分子の化学構造をSMILES記述で指定するのみで、合成反応経路を探索し、pdf ファイルとして取得することが可能である。また、探索結果をデータベースに登録すれば、任意の表示サイズでpptxファイルに変換できる。
    
<div align="center">
  <img src="./Installer/sample.jpg" width="100%">
</div>

### 動作確認済み環境
- Ubuntu 22.04.3 LTS on Windows Subsystem for Linux version 2.2.4.0

### セットアップ方法<br>
以下をターミナルで実行する。手順5の、省力化スクリプトを利用することで、作業を簡略化できる。<br>

#### 手順1: コードを作業用ディレクトリーにダウンロードする。<br>
cd hoge<br>
git clone https://github.com/kisapapa1227/retrek-ui.git<br>
cd retrek-ui<br>
git clone https://github.com/kisapapa1227/ReTReKpy.git<br>
cp .env.easy .env # docker の設定ファイルを準備する。<br>

#### 手順2:Dockerコンテナを作成する。<br>
sudo su<br>
docker-compose down<br>
docker-compose rm -f<br>
docker volume prune -f<br>
docker network prune -f<br>
docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/var/www/html -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs<br>

#### https の設定（port:80 の利用状態の確認）<br>
lsof -i:80　# ポートの利用状態を確認する。<br>
COMMAND    PID     USER   FD   TYPE DEVICE SIZE/OFF NODE NAME<br>
apache2 113106     root    4u  IPv6 864638      0t0  TCP *:80 (LISTEN)<br>
<br>
上記のように表示された場合、port:80を利用しているサービスを停止する。<br>
service apache2 stop<br>

同様に、<br>
/var/www/html が存在する場合、別名で保存するか、必要がなければ消去する。<br>
mv /var/www/html /var/www/html.org<br>
rm -rf /var/www/html<br>

#### 手順3:Dockerイメージを作成、起動する。<br>
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

ブラウザから<br>
http://localhost<br>
で接続する。<br>

#### 手順4:Docker イメージを再利用する。<br>
手順1-3で作成した Docker イメージはコンピュータ再起動後でも利用できる。<br>
cd /var/www/html/<br>
sudo ./vendor/bin/sail up -d <br>
ただし、apache2 等が起動している場合、停止する。<br>
sudo service apache2 stop<br>

#### 手順5:手順の自動化。<br>
手順1,2,3 は、このページのディレクトリー Installer に準備されているスクリプトファイルを実行することで省力化できる。<br>
あらかじめ任意のディレクトリー hoge に InstallerStep1.sh、InstallerStep2.sh、InstallerStep3.sh、InstallerStep4.sh をダウンロードする。<br>
cd hoge<br>
sh InstallerStep1.sh<br>
sh InstallerStep2.sh<br>
sh InstallerStep3.sh<br>
ただし、https の設定に関しては、必要であれば、InstallerStep3.sh の実行時に指示されるので、上述の手順を参考に、指示に従う。<br>

#### バージョンアップに関して。<br>
バージョンアップの場合、同名で旧バージョンのイメージが存在すると、そのイメージが優先的に利用されるので<br>
sh InstallerStep4.sh<br>
を実行し、存在するイメージの削除をした後<br>
sh InstallerStep3.sh<br>
を実行する。

#### ユーザーマニュアル
<a href="./Installer/retrek-ui-user-manualV1.3.pdf">ユーザーマニュアル</a>に操作を示す。
