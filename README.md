git clone https://github.com/mohamadnourpro1/stater-projects
cp .env.example .env
php artisan key:generate
composer install
php artisan command:rebuild_db
php artisan migrate
