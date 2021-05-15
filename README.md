# Belajar Bareng API
Belajar bareng is a website-based learning platform.

### Requirements
1. PHP >= 7.3
1. MYSQL

### Setup Project
1. Create file `.env` in project directory and change variable value depending your development environment. Check reference file `.env.example`. 

1. (Optional) For local development using linux, please adjust "user" value in `docker-compose.yml` with local user ID. For ubuntu, you can check by run command `$ id -u` and `$ id -g` in terminal.

1. Run `docker-compose run --rm composer composer install` to install required dependencies.

1. Run `docker-compose build && docker-compose up` to run application.

1. Run `docker-compose run --rm artisan migrate` to run migration.


### Database Design
You can view database design here: https://dbdiagram.io/d/609d54f5b29a09603d14c320

