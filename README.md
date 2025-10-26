# 1. Clone the repository
git clone https://github.com/yourusername/your-repo.git

cd your-repo

# 2. Install PHP dependencies
composer install

# 3. Copy the example environment file
cp .env.example .env

# 5. Generate the application key
php artisan key:generate

# 6. Run Migrations
php artisan Migrate

# 7. Run Seeder
php artisan db:seed

# 8. Spin up the server
php artisan serve

# 9. For Testing Run:
php artisan test
