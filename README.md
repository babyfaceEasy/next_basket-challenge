# Next Basket Tech challenge Backend
This project has two micro services written in Laravel 8 that communicates with each other making use of a message broker known as RabbitMQ.  The architecture followed here is the Event Driven Architecture (EDA) pattern. all services / technologies used are contianerised using docker and docker compose.

## Installation

1. Clone the project repository on to your laptop.
2. Go into the broker directory and run `docker compose up`. This starts up the rabbit MQ cli and admin.
3. Go into the user folder and run `docker compose up`. This starts up the user service.
   1. Once the services are up, run  `docker compose exec -it user_app bash` 
   2. Copy the content of `.env.example` into `.env`
   3. Type `composer install` to install all dependencies
   4. Run `php artisan migrate`. This creates the tables for the microservice.
4. Go into the notification folder and run `docker compose up`. This starts up the notification service.
   1. Once the services are up, run `docker compose exec -it notification_app bash`
   2. Copy the content of `.env.example` into `.env`
   3. Type `composer install` to install all dependencies.
   4. Type `php artisan queue:work` to start the listener.
NB:
1. Ensure you have docker desktop running on your laptop
2. Ensure the following ports are free:
   1. 8080 - for user microservice
   2. 8081 - for notification microservice
   3. 3306 - for MySQL database
   4. 3400 - for phpmyadmin
   5. 9000 - for user application
   6. 9001 - for notification application
3. Please follow the order given when running the services this is because user and notification service depends on the network created in the broker service.

## Usage

- Create a User on user microservice
  - Make a POST request to `http://localhost:8080/users`
  - Set the Content-Type header to `application/json`
  - Pass the `email, first_name and last_name` as a json payload

- Check if notification service received the message
  - Go the the terminal where you executed `php artisan queue:work`, you should see `Processing` and then `Done`.
  - You can also check the `storage > logs > laravel.log` file to see the messaged been logged there as an `info` object.

## Testing

- Run user microservice test
  - Open a terminal and type `docker compose exec -it user_app bash`
  - Inside there, run `vendor/bin/phpunit` to see all the test run

- Run notification microservice test
  - Open a terminal and type `docker compose exec -it notification_app bash`
  - Inside there, run `vendor/bin/phpunit` to see all the test run
- NB: 
  - Make sure you have the services (user and notification) microservices running.