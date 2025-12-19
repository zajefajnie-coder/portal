# Deployment Guide for Shared Hosting (e.g., Zenbox)

This guide covers deploying Laravel Portal Modelingowy to shared hosting environments where you have limited control over the server configuration.

## Installation from Git (Recommended)

This is the recommended method for deploying the application to Zenbox. It allows for easy updates and version control.

### Prerequisites for Git Installation

- Git repository URL (GitHub, GitLab, Bitbucket, etc.)
- SSH access to your Zenbox hosting account
- PHP 8.4+ with required extensions (pdo_mysql, redis, mbstring, openssl, etc.)
- MySQL database access
- Redis server access (host: 127.0.0.1, port: 7079)
- Composer installed
- Node.js and npm installed (for building assets)

### Preparing Your Git Repository

If you haven't created a Git repository yet:

1. **Create a repository** on GitHub, GitLab, Bitbucket, or your preferred Git hosting service
2. **Initialize Git** in your local project (if not already done):
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   ```
3. **Push to remote repository**:
   ```bash
   git remote add origin <your-repository-url>
   git branch -M main
   git push -u origin main
   ```

**Important:** Make sure `.env` is in `.gitignore` (it should be by default) and never commit sensitive credentials.

### Method 1: Automated Installation Script (Fastest)

The easiest way to deploy is using the provided deployment script:

1. **Upload the script** to your Zenbox account:
   ```bash
   # From your local machine, upload the script
   scp deploy-zenbox.sh username@zenbox-server:~/deploy-zenbox.sh
   ```

2. **SSH into your Zenbox server**:
   ```bash
   ssh username@zenbox-server
   ```

3. **Make the script executable**:
   ```bash
   chmod +x ~/deploy-zenbox.sh
   ```

4. **Run the deployment script**:
   ```bash
   ~/deploy-zenbox.sh <git-repository-url> [branch]
   ```
   
   Example:
   ```bash
   ~/deploy-zenbox.sh https://github.com/username/laravel-portal.git main
   ```

5. **Follow the prompts**:
   - The script will clone the repository
   - It will ask you to configure `.env` file (edit it with your production settings)
   - It will ask if you want to run migrations
   - It will build assets and configure everything automatically

The script will:
- Clone your Git repository to `~/laravel-portal/`
- Install all PHP and Node dependencies
- Set up the `.env` file (from `.env.example`)
- Run migrations (if you choose)
- Build production assets
- Configure `public_html/` directory
- Set proper permissions
- Create storage symlink
- Optimize the application (cache config, routes, views)

**Log file:** All deployment actions are logged to `~/deploy.log`

### Method 2: Manual Git Installation

If you prefer to install manually or the script doesn't work for your setup:

1. **Clone the repository**:
   ```bash
   cd ~
   git clone -b main <your-repository-url> laravel-portal
   cd laravel-portal
   ```

2. **Set up environment file**:
   ```bash
   cp .env.example .env
   # Edit .env with your production settings (see Step 5 below)
   ```

3. **Install dependencies**:
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install
   ```

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Configure `.env` file** with your production settings:
   ```env
   APP_NAME="Laravel Portal Modelingowy"
   APP_ENV=production
   APP_KEY=  # Already generated above
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password

   SESSION_DRIVER=redis
   CACHE_STORE=redis
   QUEUE_CONNECTION=redis

   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=your_redis_password
   REDIS_PORT=7079
   ```

6. **Publish vendor assets**:
   ```bash
   php artisan vendor:publish --tag=laravel-assets --force
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   ```

7. **Run migrations**:
   ```bash
   php artisan migrate --force
   php artisan db:seed  # If you have seeders
   ```

8. **Build production assets**:
   ```bash
   npm run build
   ```

9. **Set up public directory**:
   ```bash
   # Copy public files
   cp -r public/* ~/public_html/
   
   # Update index.php
   cat > ~/public_html/index.php << 'EOF'
   <?php

   use Illuminate\Http\Request;

   define('LARAVEL_START', microtime(true));

   if (file_exists($maintenance = __DIR__.'/../laravel-portal/storage/framework/maintenance.php')) {
       require $maintenance;
   }

   require __DIR__.'/../laravel-portal/vendor/autoload.php';

   (require_once __DIR__.'/../laravel-portal/bootstrap/app.php')
       ->handleRequest(Request::capture());
   EOF
   
   # Create storage symlink
   cd ~/public_html
   ln -s ../laravel-portal/storage/app/public storage
   ```

10. **Set permissions**:
    ```bash
    chmod -R 755 ~/laravel-portal/storage
    chmod -R 755 ~/laravel-portal/bootstrap/cache
    ```

11. **Optimize application**:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

### Updating the Application (Git Method)

To update your application after making changes:

1. **Push changes to your Git repository** (from your local machine):
   ```bash
   git add .
   git commit -m "Update description"
   git push origin main
   ```

2. **SSH into Zenbox** and update:
   ```bash
   ssh username@zenbox-server
   cd ~/laravel-portal
   git pull origin main
   composer install --optimize-autoloader --no-dev
   npm install
   npm run build
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

Or use the automated update script (if you create one):
```bash
cd ~/laravel-portal
git pull
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## Manual Installation (Alternative Method)

If you don't want to use Git, you can upload files manually via FTP/SFTP:

## Prerequisites

- PHP 8.4+ with required extensions (pdo_mysql, redis, mbstring, openssl, etc.)
- MySQL database access
- Redis server access (host: 127.0.0.1, port: 7079)
- Composer installed
- Node.js and npm installed (for building assets)
- SSH access to your hosting account

## Folder Structure for Shared Hosting

On shared hosting, you typically have:
- `~/laravel-portal/` - Main Laravel application (outside public_html)
- `~/public_html/` - Web-accessible directory (maps to Laravel's `public/`)

## Step-by-Step Deployment

### 1. Upload Project Files

Upload all project files to `~/laravel-portal/` directory (except `public/` folder contents).

### 2. Set Up Public Directory

Copy contents of `public/` folder to `~/public_html/`:

```bash
cp -r public/* ~/public_html/
```

### 3. Update index.php

Edit `~/public_html/index.php` to point to the correct Laravel installation:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../laravel-portal/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel-portal/vendor/autoload.php';

(require_once __DIR__.'/../laravel-portal/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

### 4. Create Storage Symlink

Create a symbolic link from `public_html/storage` to `laravel-portal/storage/app/public`:

```bash
cd ~/public_html
ln -s ../laravel-portal/storage/app/public storage
```

### 5. Set Up Environment File

Copy `.env.example` to `.env` in `~/laravel-portal/`:

```bash
cd ~/laravel-portal
cp .env.example .env
```

Edit `.env` with your production settings:

```env
APP_NAME="Laravel Portal Modelingowy"
APP_ENV=production
APP_KEY=  # Generate with: php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=krzyszton_cursor
DB_USERNAME=krzyszton_cursor
DB_PASSWORD=Alicja2025##

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=fnBDJ&fzhU&S7J
REDIS_PORT=7079
```

### 6. Install Dependencies

```bash
cd ~/laravel-portal
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 7. Generate Application Key

```bash
php artisan key:generate
```

### 8. Run Migrations

```bash
php artisan migrate --force
php artisan db:seed  # If you have seeders
```

### 9. Publish Vendor Assets

```bash
php artisan vendor:publish --tag=laravel-assets --force
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 10. Set Permissions

```bash
chmod -R 755 ~/laravel-portal/storage
chmod -R 755 ~/laravel-portal/bootstrap/cache
```

### 11. Configure Redis

Ensure Redis is running and accessible at `127.0.0.1:7079` with the password `fnBDJ&fzhU&S7J`.

### 12. Set Up Cron Job (Optional)

For queue processing, add to crontab:

```bash
* * * * * cd ~/laravel-portal && php artisan schedule:run >> /dev/null 2>&1
```

## Important Security Notes

1. **Never commit `.env` file** - It contains sensitive credentials
2. **Set `APP_DEBUG=false`** in production
3. **Use HTTPS** - Update `APP_URL` to use `https://`
4. **Restrict file permissions** - Only storage and cache directories need write access
5. **Keep Laravel updated** - Regularly update dependencies for security patches

## Troubleshooting

### 500 Internal Server Error

- Check `~/laravel-portal/storage/logs/laravel.log` for errors
- Verify file permissions on `storage/` and `bootstrap/cache/`
- Ensure `.env` file exists and is properly configured

### Assets Not Loading

- Run `npm run build` to compile React/Vite assets
- Verify `public_html/build/` directory exists
- Check that Vite manifest is generated correctly

### Database Connection Issues

- Verify database credentials in `.env`
- Check MySQL user permissions
- Ensure database exists

### Redis Connection Issues

- Verify Redis is running: `redis-cli -h 127.0.0.1 -p 7079 -a fnBDJ&fzhU&S7J ping`
- Check firewall rules if Redis is on a different server
- Fallback to `file` driver if Redis is unavailable (not recommended for production)

## Post-Deployment Checklist

- [ ] Application loads without errors
- [ ] User registration works
- [ ] Login/logout functions correctly
- [ ] Portfolio creation/editing works
- [ ] Image uploads work
- [ ] Admin panel is accessible (for admin users)
- [ ] Redis caching is working
- [ ] Sessions persist correctly
- [ ] All API endpoints respond correctly
- [ ] React routing works (SPA navigation)

## Maintenance

### Updating the Application

**If installed via Git (Recommended):**

```bash
cd ~/laravel-portal
git pull origin main  # or your branch name
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**If installed manually (FTP/SFTP):**

Upload new files via FTP/SFTP, then run:

```bash
cd ~/laravel-portal
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Clearing Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Support

For issues specific to your hosting provider, consult their documentation or support team.



