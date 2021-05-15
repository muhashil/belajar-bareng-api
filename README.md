# Belajar Bareng API
Belajar bareng adalah platform yang dapat digunakan oleh seseorang untuk berbagi pengetahuan kepada masyarakat secara online.

### Requirements
1. PHP >= 7.3
1. MYSQL

### Setup Project
1. Create file `.env` in project directory and change variable value depending your development environment. Check reference file `.env.example`. 

1. Run `docker-compose run --rm composer composer install` to install required dependencies.

1. Run `docker-compose build && docker-compose up` to run application.

1. Run `docker-compose run --rm artisan migrate` to run migration.
