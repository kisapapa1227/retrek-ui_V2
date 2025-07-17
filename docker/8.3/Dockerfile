FROM ubuntu:22.04

LABEL maintainer="Taylor Otwell"

ARG WWWGROUP
ARG NODE_VERSION=20
ARG POSTGRES_VERSION=15

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80"

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && mkdir -p /etc/apt/keyrings \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 dnsutils librsvg2-bin fswatch ffmpeg \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y php8.3-cli php8.3-dev \
       php8.3-pgsql php8.3-sqlite3 php8.3-gd \
       php8.3-curl \
       php8.3-imap php8.3-mysql php8.3-mbstring \
       php8.3-xml php8.3-zip php8.3-bcmath php8.3-soap \
       php8.3-intl php8.3-readline \
       php8.3-ldap \
       php8.3-msgpack php8.3-igbinary php8.3-redis php8.3-swoole \
       php8.3-memcached php8.3-pcov php8.3-imagick php8.3-xdebug \
    && curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && npm install -g pnpm \
    && npm install -g bun \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /etc/apt/keyrings/yarn.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt jammy-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y yarn \
    && apt-get install -y mysql-client \
    && apt-get install -y postgresql-client-$POSTGRES_VERSION \
    && apt-get install -y docker.io \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.3

# 環境変数の設定（インタラクティブな質問を避けるため）
ENV DEBIAN_FRONTEND=noninteractive

# 必要なパッケージのインストール

RUN apt-get update --fix-missing && apt-get install -y \
    software-properties-common \
    build-essential \
    libssl-dev \
    libffi-dev \
    python3-dev \
    python3-pip \
    python3-venv \
    wget \
    git  \
    cron \
    gnupg 

# Python 3.10のインストール
RUN add-apt-repository ppa:deadsnakes/ppa && \
    apt-get update && \
    apt-get install -y python3.10 python3.10-dev python3.10-venv python3-pip && \
    update-alternatives --install /usr/bin/python3 python3 /usr/bin/python3.10 1 && \
    update-alternatives --install /usr/bin/python python /usr/bin/python3.10 1 && \
    update-alternatives --set python3 /usr/bin/python3.10

# pipのアップグレードと必要なPythonパッケージのインストール

RUN python3.10 -m pip install --upgrade pip setuptools wheel && \
    python3.10 -m pip install tensorflow==2.15 && \
    python3.10 -m pip install \
    rdkit==2023.9.5 \
    rdchiral==1.1.0 \
    oddt==0.7 \
    mendeleev==0.15.0 \
    MolVS==0.1.1 \
    indigo==1.0.5 \
    torch==2.2.1 \
    torchaudio==2.2.1 \
    torchvision==0.17.1 \
    py4j==0.10.9.7 \
    tqdm==4.66.2 \
    joblib==1.3.2 \
    numpy==1.25.2 \
    scipy==1.11.4 \
    scikit-learn==1.2.2 \
    matplotlib==3.7.1 \
    scikit-image==0.19.3 \
    IPython==8.27.0 \
    reportlab==4.2.2 \
    svglib==1.5.1 \
    python-pptx==1.0.2 \
    Levenshtein==0.25.1

# OpenBabel 3.0のインストール
# 必要な依存関係のインストール
RUN apt-get update && apt-get install -y \
    cmake \
    g++ \
    libxml2-dev \
    zlib1g-dev \
    imagemagick

# OpenBabel 3.0のソースをダウンロードし、解凍
RUN wget https://github.com/openbabel/openbabel/archive/openbabel-3-0-0.tar.gz && \
    tar -zxvf openbabel-3-0-0.tar.gz


# # Set permissions
# RUN mkdir -p /var/www/html/public/images
# RUN chown -R www-data:www-data /var/www/html/public/images
# RUN chmod -R 775 /var/www/html/public/images

# ビルドディレクトリに移動し、ビルドとインストールを実行(ビルドディレクトリはこれでいいのか?)
RUN cd openbabel-openbabel-3-0-0 && \
    mkdir build && \
    cd build && \
    cmake .. && \
    make && \
    make install


# インストール後のクリーンアップ
RUN rm -rf /openbabel-3-0-0.tar.gz /openbabel-openbabel-3-0-0

RUN python3.10 -m pip install --upgrade git+https://github.com/clinfo/kGCN.git

RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.3/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000

ENTRYPOINT ["start-container"]
