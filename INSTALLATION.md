# Installation Guide

## Quick Start

### 1. Install Dependencies

```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database and Redis credentials.

### 3. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed roles
php artisan db:seed --class=DatabaseSeeder
```

### 4. Storage Setup

```bash
php artisan storage:link
```

### 5. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000`

## Creating First Admin User

After running migrations, create an admin user via tinker:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password'),
    'is_approved' => true,
]);

$user->assignRole('admin');
```

## Troubleshooting

### Redis Connection Issues

If Redis is not available, temporarily change in `.env`:

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### Permission Errors

Ensure storage and cache directories are writable:

```bash
chmod -R 775 storage bootstrap/cache
```

### Vite Assets Not Loading

Make sure Vite dev server is running:

```bash
npm run dev
```

And that `APP_URL` in `.env` matches your local URL.



