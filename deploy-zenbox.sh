#!/bin/bash

# Laravel Portal Modelingowy - Deployment Script for Zenbox
# This script installs the application from Git repository to shared hosting

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="$HOME/laravel-portal"
PUBLIC_DIR="$HOME/public_html"
LOG_FILE="$HOME/deploy.log"
BRANCH="${BRANCH:-main}"

# Functions
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# Check if Git repository URL is provided
if [ -z "$1" ]; then
    error "Usage: $0 <git-repository-url> [branch]"
    error "Example: $0 https://github.com/username/laravel-portal.git main"
fi

GIT_REPO_URL="$1"
if [ -n "$2" ]; then
    BRANCH="$2"
fi

log "Starting deployment from Git repository: $GIT_REPO_URL (branch: $BRANCH)"

# Check system requirements
log "Checking system requirements..."

command -v php >/dev/null 2>&1 || error "PHP is not installed"
command -v composer >/dev/null 2>&1 || error "Composer is not installed"
command -v npm >/dev/null 2>&1 || error "npm is not installed"
command -v git >/dev/null 2>&1 || error "Git is not installed"

PHP_VERSION=$(php -r 'echo PHP_VERSION;')
log "PHP version: $PHP_VERSION"

# Check if APP_DIR already exists
if [ -d "$APP_DIR" ]; then
    warning "Directory $APP_DIR already exists!"
    read -p "Do you want to backup and remove it? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        BACKUP_DIR="${APP_DIR}_backup_$(date +%Y%m%d_%H%M%S)"
        log "Creating backup to $BACKUP_DIR"
        mv "$APP_DIR" "$BACKUP_DIR"
    else
        error "Deployment cancelled. Please remove or backup $APP_DIR manually."
    fi
fi

# Check if PUBLIC_DIR exists
if [ ! -d "$PUBLIC_DIR" ]; then
    error "Public directory $PUBLIC_DIR does not exist!"
fi

# Clone repository
log "Cloning repository..."
if [ -d "${APP_DIR}_temp" ]; then
    rm -rf "${APP_DIR}_temp"
fi

git clone -b "$BRANCH" "$GIT_REPO_URL" "${APP_DIR}_temp" || error "Failed to clone repository"

# Move to final location
log "Moving files to final location..."
mv "${APP_DIR}_temp" "$APP_DIR"

# Navigate to application directory
cd "$APP_DIR" || error "Failed to navigate to $APP_DIR"

# Check if .env exists, if not copy from .env.example
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        log "Creating .env file from .env.example"
        cp .env.example .env
        warning "Please edit .env file with your production settings before continuing!"
        read -p "Press Enter after you have configured .env file..."
    else
        error ".env.example file not found!"
    fi
else
    log ".env file already exists, skipping copy"
fi

# Install PHP dependencies
log "Installing PHP dependencies with Composer..."
composer install --optimize-autoloader --no-dev --no-interaction || error "Composer install failed"

# Install Node dependencies
log "Installing Node dependencies..."
npm install || error "npm install failed"

# Generate application key if not set
log "Generating application key..."
php artisan key:generate --force || warning "Failed to generate application key (may already be set)"

# Publish vendor assets
log "Publishing vendor assets..."
php artisan vendor:publish --tag=laravel-assets --force --no-interaction || warning "Failed to publish Laravel assets"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --no-interaction || warning "Failed to publish Spatie Permission assets"
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --no-interaction || warning "Failed to publish Sanctum assets"

# Run migrations
log "Running database migrations..."
read -p "Do you want to run migrations now? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force || error "Migration failed"
    
    read -p "Do you want to run seeders? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed --force || warning "Seeding failed"
    fi
else
    warning "Skipping migrations. Remember to run: php artisan migrate --force"
fi

# Build production assets
log "Building production assets..."
npm run build || error "Asset build failed"

# Set up public directory
log "Setting up public directory..."

# Backup existing index.php if it exists
if [ -f "$PUBLIC_DIR/index.php" ]; then
    cp "$PUBLIC_DIR/index.php" "$PUBLIC_DIR/index.php.backup"
fi

# Copy public files
log "Copying public files..."
cp -r public/* "$PUBLIC_DIR/" || error "Failed to copy public files"

# Update index.php to point to Laravel installation
log "Updating index.php..."
cat > "$PUBLIC_DIR/index.php" << 'EOF'
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
log "Creating storage symlink..."
if [ -L "$PUBLIC_DIR/storage" ]; then
    rm "$PUBLIC_DIR/storage"
fi
if [ -d "$PUBLIC_DIR/storage" ]; then
    warning "Storage directory already exists in public_html, skipping symlink creation"
else
    ln -s ../laravel-portal/storage/app/public "$PUBLIC_DIR/storage" || warning "Failed to create storage symlink"
fi

# Set permissions
log "Setting permissions..."
chmod -R 755 storage bootstrap/cache || warning "Failed to set permissions"

# Clear and cache configuration
log "Optimizing application..."
php artisan config:cache || warning "Failed to cache config"
php artisan route:cache || warning "Failed to cache routes"
php artisan view:cache || warning "Failed to cache views"

# Test Redis connection (optional)
log "Testing Redis connection..."
php artisan tinker --execute="
try {
    \Illuminate\Support\Facades\Redis::connection()->ping();
    echo 'Redis connection: OK';
} catch (\Exception \$e) {
    echo 'Redis connection: FAILED - ' . \$e->getMessage();
}
" || warning "Redis connection test failed or Redis not configured"

log "Deployment completed successfully!"
log "Log file saved to: $LOG_FILE"
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Deployment Summary:${NC}"
echo -e "${GREEN}========================================${NC}"
echo "Application directory: $APP_DIR"
echo "Public directory: $PUBLIC_DIR"
echo "Branch: $BRANCH"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Verify .env file configuration"
echo "2. Test the application in your browser"
echo "3. Set up cron job for scheduled tasks (if needed)"
echo "4. Configure SSL certificate (if not already done)"
echo ""
echo -e "${YELLOW}To update the application in the future, run:${NC}"
echo "cd $APP_DIR && git pull && composer install --optimize-autoloader --no-dev && npm install && npm run build && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache"
