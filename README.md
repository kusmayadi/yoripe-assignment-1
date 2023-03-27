## Installation & Setup

1. Clone the repo.

    `git clone git@github.com:kusmayadi/yoripe-assignment-1.git`
    
2. Create `.env` or copy from `.env.example`

    `cp .env.example .env`
    
3. Generate key.

    `php artisan key:generate`
    
4. Change database settings

        B_HOST=mysql
        DB_PORT=3306
        DB_DATABASE=assignment_1
        DB_USERNAME=sail
        DB_PASSWORD=password`

5. Run composer install to install packages

    `composer install`
    
6. Run migrations

    `php artisan migrate`
    
7. Run seeder

    `php artisan db:seed`

## Tests

1. Create database for testing in your mysql installation
2. Create `.env.testing` or copy from your `.env`
3. Change database settings in your `.env.testing`

        B_HOST=mysql
        DB_PORT=3306
        DB_DATABASE=testing
        DB_USERNAME=sail
        DB_PASSWORD=password`
        
4. Run all the tests

    `php artisan test`
