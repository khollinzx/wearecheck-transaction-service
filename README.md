# Development Guide
## Local Setup
### Prerequisites

- PHP 8.2+
- MySQL 8.0+
- PhpMyAdmin
- Postman

## Clone the repository:

```
git clone https://github.com/khollinzx/wearecheck-transaction-service.git

cd wearecheck-transaction-service
```

## Install dependencies:

```
composer install
```

## Configure environment:

```
cp .env.example .env 
php artisan key:generate
```

## Set up environment and database:
```
php artisan migrate
php artisan passport:key --force
php artisan passport:client --personal
php artisan serve
```
### Using Sail ###
```
./vendor/bin/sail up
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan passport:key --force
./vendor/bin/sail artisan passport:client --personal
```
## Postman Doc.
```
https://documenter.getpostman.com/view/10224661/2sAXxY4oNx
```
## Testing
```
php artisan test --filter=Feature
OR
./vendor/bin/sail artisan test --filter=Feature
```




