## About project

This is a Laravel-based online technical shop project. This README provides setup instructions, including JWT authentication using tymon/jwt-auth and API documentation using Swagger.

## Requirements

- PHP 8.1+
- Composer
- Laravel 12+
- MySQL / PostgreSQL / SQLite / SQL Server

## Installation

### Step 1: Clone the Repository

```
git clone https://github.com/abgar10a/laravel-shop
cd PhpShop
```

### Step 2: Install Dependencies

```
composer install
```

### Step 3: Set Up Environment

Copy ".env.example" and create the .env file with your database and application settings.

### Step 4: Generate Application Key

```
php artisan key:generate
```

### Step 5: Configure Database & Migrate

Ensure your database settings are correct in .env, then run:

```
php artisan migrate
```

### Step 6: Generate JWT Secret Key

```
php artisan jwt:secret
```

### Running the Application

```
php artisan serve
```

Your application should be available at http://127.0.0.1:8000/.
