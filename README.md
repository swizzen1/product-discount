# Product Discount – Laravel Cart API

A Laravel-based cart and product discount system providing REST-style API endpoints for managing cart products.

---

## 🚀 Installation & Setup

Follow the steps below to run the project locally.

### 1️⃣ Clone the repository
```bash
git clone https://github.com/swizzen1/product-discount.git
cd product-discount
```

### 2️⃣ Install PHP dependencies
```bash
composer install
```

### 3️⃣ Copy the environment file
```bash
cp .env.example .env
```

### 4️⃣ Generate the application key
```bash
php artisan key:generate
```

### 5️⃣ Run database migrations
```bash
php artisan migrate
```

### 6️⃣ Run database seeders
```bash
php artisan db:seed
```

### 7️⃣ Start the development server
```bash
php artisan serve
```

The application will be available at:

```
http://127.0.0.1:8000
```

---

## 🧪 Running Tests

Run the test suite using:

```bash
php artisan test
```

---

## 🔗 API Routes

### 1️⃣ Get products from cart
- **Method:** `GET`
- **URL:**
```
http://127.0.0.1:8000/getUserCart
```

---

### 2️⃣ Add product to cart
- **Method:** `POST`
- **Description:**  
  `quantity` is optional. Defaults to **1** if not provided.
- **URL:**
```
http://127.0.0.1:8000/addProductInCart?product_id=5
```

---

### 3️⃣ Remove product from cart
- **Method:** `POST`
- **URL:**
```
http://127.0.0.1:8000/removeProductFromCart?product_id=5
```

---

### 4️⃣ Change product quantity in cart
- **Method:** `POST`
- **URL:**
```
http://127.0.0.1:8000/setCartProductQuantity?product_id=5
```

---

## 📌 Notes

- Ensure database credentials are set correctly in `.env`
- Default database driver: **MySQL**
- Laravel application uses standard REST-style endpoints

---

## 👨‍💻 Author

Built with ❤️ using **Laravel**
