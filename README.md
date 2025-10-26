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
php artisan migrate

# 7. Run Seeder
php artisan db:seed

# 8. Spin up the server
php artisan serve

# 9. For Testing Run:
php artisan test

Routes:

# 1. Get Products from cart (GET):  http://127.0.0.1:8000/getUserCart

# 2. Remove Products From cart (POST):  http://127.0.0.1:8000/addProductInCart?product_id=5 removeProductFromCart

# 3. ADD Products IN cart (quantity is optional quantity = 1 as default if not provided): (POST) http://127.0.0.1:8000/addProductInCart?product_id=5

# 4. Change product quantity in cart (POST) http://127.0.0.1:8000/setCartProductQuantity/product_id=5
