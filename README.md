# Acilia test
Technical test

## Instalation
Install dependencies:

```php
composer install
```

## Usage
Init [symfony local server](https://symfony.com/doc/4.4/setup/symfony_server.html#installation):
```php
symfony server:start -d
```

Init docker database containers:
```php
docker-compose start
```
Configure .env files (.env.local for docker default configuration) :
```
DATABASE_URL=mysql://root:password@127.0.0.1:3306/main?serverVersion=5.7
```
Load database schema:
```php
php bin/console doctrine:migrations:migrate
```

Load fixture data:
```php
php bin/console doctrine:fixtures:load
```

## Consume RESTAPI

I used a [postman](https://www.postman.com/) collection for test the RESTApi endpoints.


https://www.getpostman.com/collections/b693e9c622d860b50354