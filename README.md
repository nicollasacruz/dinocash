# DinoCash

## Setup

This project run with Laravel 10 + vueJs

For up the application run command:

```shell
docker-compose -f docker-compose-dev.yml up -d
```

For up the application run dev command:

```shell
cp .env-example .env && chmod 777 -R volumes/dinocash/storage
```

```shell
docker-compose exec -u root php_dino bash
```

```shell
composer install && php artisan migrate
```

```shell
npm install && npm run dev
```
