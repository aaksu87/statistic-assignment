## Environment
Run `docker-compose up -d` in the project folder, then use
`docker exec -it <name_of_php_container> /bin/bash` to enter the PHP container for composer install and running the unit-test.

## Dependencies
Run `composer install`

## Create Data
Run `php bin/console doctrine:database:create` to create 'customer_alliance' database

Run `php bin/console doctrine:migration:migrate` to create tables

Run `php bin/console doctrine:fixtures:load` to create faker data

## Call Api

[POST] `http://localhost:8080/review-statistic`

with the values of
`(int)hotel_id, date("Y-m-d")start_date, date("Y-m-d")end_date`

Note : You may find postman collection on public\assignment.postman_collection.json

## Unit Test

In php container;

`php bin/phpunit`