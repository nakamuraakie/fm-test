# coachtechフリマ

### 環境構築
1.git clone git@github.com:Estra-Coachtech/laravel-docker-template.git

2.cd laravel-docker-template

3.DockerDesktopアプリを立ち上げる

4.docker-compose up -d --build

### Laravel環境構築

docker-compose exec php bash

composer install

composer require livewire/livewire

cp .env.example .env

.env ファイルの一部を以下のように編集


DB_HOST=mysql
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
