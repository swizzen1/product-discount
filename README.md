# 1. Clone the repository
git clone https://github.com/yourusername/your-repo.git

cd your-repo

# 2. Install PHP dependencies
composer install

# 3. Copy the example environment file
cp .env.example .env

# 4. Run Migrations
php artisan Migrate

# 5. Run Seeder
php artisan db:seed

# 5. Spin up the server
php artisan serve
