### FlexClub Project

This test uses PHP 7.3.2, Laravel, 5.7.27, nginx 1.15.8, PostgreSQL 11.2 and Docker

## Assumptions


## Requirements

* Docker (https://www.docker.com/community-edition#/download)
* Docker Compose (https://docs.docker.com/compose/)

## Installation

1. Clone the repo

2. Start the Docker containers, which will download and install all the required software

        docker-compose up -d

3. Create a .env file

        docker-compose exec app cp .env.example .env

4. Configure the database connection, edit the .env file and set the following:

        DB_CONNECTION=pgsql
        DB_HOST=db
        DB_PORT=5432
        DB_DATABASE=flexclub
        DB_USERNAME=flexclub
        DB_PASSWORD='Fl3X(1u8'

5. Set the Eventbrite API Key, edit the .env file and set the following:

        EVENTBRITE_API_KEY=XXXXXXXXXXXXXXXXXXXXX
        
6. Run composer update

        docker-compose exec app composer update

7. Generate an applicate key

        docker-compose exec app php artisan key:generate

8. Run the database migrations

        docker-compose exec app php artisan migrate

9. Seed the databasse

        docker-compose exec app php artisan db:seed

10. The App listens for incoming connections at http://127.0.0.1 (on port 80, assuming that port 80 is not already in use)

## Importing the data from the EventBrite API

        docker-compose exec app php artisan events:import

## Accessing the API Endpoints

The API endpointss are accessible at:

    http://127.0.0.1/events
    http://127.0.0.1/events/{event_uuid}
    http://127.0.0.1/organizers/details/{organizer_uuid}
    http://127.0.0.1/stats

## Automated Tests

Usually I would prefer to write Automated/Unit tests for my code, but unfortunately due to time constraints, I was not able to do so.
