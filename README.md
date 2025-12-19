# Laravel Portal Modelingowy

A professional modeling & creative industry portal built with Laravel 11 and React 18.

## Features

- **Public Portfolio Showcase** - Browse portfolios without login
- **Advanced Search** - Filter by profession, city, and tags
- **User Portfolios** - Create, edit, and manage portfolios with multiple images
- **Tagging System** - Tag portfolios for better discoverability
- **Admin Panel** - User management, content moderation, and statistics
- **Role-Based Access** - Admin, Moderator, and User roles
- **Redis Integration** - Sessions, cache, and queues via Redis

## Tech Stack

- **Backend**: Laravel 11.x (PHP 8.4)
- **Frontend**: React 18 + Vite (SPA)
- **Authentication**: Laravel Sanctum (stateful API)
- **Permissions**: Spatie Laravel Permission
- **Database**: MySQL
- **Cache/Sessions/Queues**: Redis
- **Styling**: Tailwind CSS + DaisyUI

## Git Repository Setup

Before deploying to production, you should set up a Git repository for version control and easy deployment.

### Initializing Git Repository

If this project doesn't have a Git repository yet, follow these steps:

1. **Check if Git is initialized**:
   ```bash
   git status
   ```
   If you see "not a git repository", proceed to step 2.

2. **Initialize Git repository**:
   ```bash
   git init
   ```

3. **Add all files** (`.gitignore` will automatically exclude sensitive files):
   ```bash
   git add .
   ```

4. **Create initial commit**:
   ```bash
   git commit -m "Initial commit - Laravel Portal Modelingowy"
   ```

5. **Create a repository** on GitHub, GitLab, Bitbucket, or your preferred Git hosting service.

6. **Add remote repository**:
   ```bash
   git remote add origin <your-repository-url>
   # Example: git remote add origin https://github.com/username/laravel-portal.git
   ```

7. **Push to remote**:
   ```bash
   git branch -M main
   git push -u origin main
   ```

### Important Notes

- **Never commit `.env` file** - It contains sensitive credentials and is already in `.gitignore`
- **Never commit `vendor/` or `node_modules/`** - These are also in `.gitignore`
- **Use meaningful commit messages** - Describe what changes you made
- **Push regularly** - Keep your remote repository up to date

### Making Changes and Pushing Updates

After making changes to your code:

```bash
# Check what files have changed
git status

# Add specific files or all changes
git add .
# or
git add path/to/specific/file

# Commit changes
git commit -m "Description of changes"

# Push to remote repository
git push origin main
```

For detailed deployment instructions using Git, see [DEPLOYMENT.md](DEPLOYMENT.md).

## Installation

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 18+ and npm
- MySQL 8.0+
- Redis 7.0+

### Setup Steps

1. **Clone the repository**

```bash
git clone <repository-url>
cd laravel-portal-modelingowy
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Install Node dependencies**

```bash
npm install
```

4. **Configure environment**

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database and Redis credentials.

5. **Run migrations**

```bash
php artisan migrate
```

6. **Publish vendor assets**

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

7. **Create storage symlink**

```bash
php artisan storage:link
```

8. **Build frontend assets**

```bash
npm run dev  # For development
# or
npm run build  # For production
```

9. **Start development server**

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Database Configuration

Default database settings (update in `.env`):

- Database: `krzyszton_cursor`
- Username: `krzyszton_cursor`
- Password: `Alicja2025##`

## Redis Configuration

Default Redis settings (update in `.env`):

- Host: `127.0.0.1`
- Port: `7079`
- Password: `fnBDJ&fzhU&S7J`

## User Roles

The application uses three default roles:

- **admin** - Full access to all features
- **moderator** - Can moderate content and manage users
- **user** - Standard user with portfolio management

## API Endpoints

### Public Endpoints

- `GET /api/portfolios` - List public portfolios
- `GET /api/portfolios/{id}` - View portfolio details
- `GET /api/profiles` - List public profiles
- `GET /api/profiles/{id}` - View profile details
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user

### Authenticated Endpoints

- `GET /api/auth/user` - Get current user
- `POST /api/auth/logout` - Logout user
- `GET /api/my-portfolios` - Get user's portfolios
- `POST /api/portfolios` - Create portfolio
- `PUT /api/portfolios/{id}` - Update portfolio
- `DELETE /api/portfolios/{id}` - Delete portfolio

### Admin Endpoints

- `GET /api/admin/dashboard` - Dashboard statistics
- `GET /api/admin/users` - List users
- `POST /api/admin/users/{id}/approve` - Approve user
- `POST /api/admin/users/{id}/ban` - Ban user
- `POST /api/admin/users/{id}/unban` - Unban user
- `POST /api/admin/users/{id}/role` - Assign role
- `GET /api/admin/reported-images` - List reported images
- `POST /api/admin/images/{id}/hide` - Hide image
- `POST /api/admin/images/{id}/unhide` - Unhide image
- `DELETE /api/admin/images/{id}` - Delete image

## Security Features

- **SQL Injection Protection** - Using Eloquent ORM with parameter binding
- **XSS Protection** - Tag sanitization and HTML escaping
- **CSRF Protection** - Laravel Sanctum CSRF tokens
- **Authentication** - Laravel Sanctum stateful authentication
- **Authorization** - Role-based access control with Spatie Permissions

## Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed shared hosting deployment instructions.

## License

This project is proprietary software.

## Support

For issues and questions, please contact the development team.



