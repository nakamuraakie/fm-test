# coachtechフリマ

### 環境構築
1.git clone git@github.com:Estra-Coachtech/laravel-docker-template.git

2.cd laravel-docker-template

3.DockerDesktopアプリを立ち上げる

4.docker-compose up -d --build

### Laravel環境構築

1.docker-compose exec php bash

2.composer install

3.composer require livewire/livewire

4.cp .env.example .env

5. .env ファイルの一部を以下のように編集


```env
DB_HOST=mysql
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

6.php artisan key:generate

7.php artisan migrate

8.php artisan db:seed

## user のログイン用初期データ


## 使用技術

・MySQL 8.0.26

・PHP 7.3以上 または 8.0以上

・Laravel 8

・nginx 1.21.1

## URL

・ユーザー登録画面: http://localhost/register

・商品一覧画面: http://localhost/

・phpMyAdmin: http://http:/localhost:8080


## ER図

![ER図](docs/er.png)

