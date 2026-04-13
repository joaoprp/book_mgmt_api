# Book Management API

Description TBA

## Setting up

We need as dependency: 

- PHP 8.4+
- PostgreSQL

To set the application up, we need to expect the base set up from php + composer [from here](https://laravel.com/docs/13.x/installation#installing-php), plus installing Postgres. As Postgres installation may vary based on OS, I'm skipping those instructions until I get to the docker setting up phase further down the project

## Running the application

Once php, composer and postgres are set, you'll need to run migrations with `php artisan migrate`, and then you can run `php artisan serve`.

## Running endpoints

In this initial commit, we're expecting that API requests do send a header `Accept: application/json` for proper routing. This will be addressed in future commits
There's a folder called `requests` that implements httpYac support to run endpoints, as well as JetBrains IDEs that are able to internally run `.http` files to run and test requests
