# Banking account V1 ⚡️

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

You'll need [Git](https://git-scm.com) and [Docker](https://www.docker.com/products/docker-desktop).


## How To Use 

From your command line, clone and run Bank account:

```bash
# Clone this repository
git clone https://github.com/renatocosta/turnoverbnb.git

# Go into the repository
cd turnoverbnb

### Docker Commands
BUILD IMAGE : docker-compose build &&  docker-compose up -d

### Docker Commands
docker-compose exec app bash -c "cd framework && composer install"
docker-compose exec app bash -c "cd framework && php artisan migrate"
docker-compose exec app bash -c "cd framework && php artisan db:seed --class=UserSeeder"
docker-compose exec app bash -c "cd framework && php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider""
docker-compose exec app bash -c "cd framework && php artisan l5-swagger:generate"
docker-compose exec app bash -c "cd framework && php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider""
```

## Unit testing
```
1) Bank Account: docker-compose exec app framework/vendor/bin/phpunit --testsuite BankAccount --coverage-html framework/storage/tests

2) Bank Account Operations: docker-compose exec app framework/vendor/bin/phpunit --testsuite BankAccountOperations --coverage-html framework/storage/tests

3) End-to-End: docker-compose exec php-fpm framework/vendor/bin/phpunit --testsuite BankSystem --coverage-html framework/storage/tests

### Coverage Report: framework/storage/tests/
```
![Image](./assets/suite-tests.png?raw=true)

## Let's Run the Application 
```
http://localhost/api/documentation

### Logs: framework/storage/logs/laravel.log
```

## Event Storming

Go through all of the learning journey using Event Storming for understanding the business needs as shown below

### Steps
![Image](./assets/EventStorming.jpg?raw=true)

## Bounded contexts
![Image](./assets/EventStormingOutcome.jpg?raw=true)

[BankAccount](Domains/Context/BankAccount)

[BankAccountOperations](Domains/Context/BankAccountOperations)

## Warning
I am not taking security, performance, concurrency and among other concerns into consideration.  
I am also aware of low unit testing code coverage.  

## Improvements
Repository method names should be replaced with ones that meet the good practices.  
Swagger URL root should be replaced with a CONSTANT.  
Replace HTTP response codes with the right ones.  
In order to make sure the right balance I strongly recommend use Event Sourcing as an approach to capture every changing state.  
Add authentication in order to protect the routes as well.  
Allow creating new users.

## Knowledgements
Domain-Driven Design, Event Storming
