# URL Shortener Application - Laravel

A complete URL shortener application built with Laravel featuring role-based access control.

## Features
- 5 User Roles: SuperAdmin, Admin, Member, Sales, Manager
- URL Shortening with click tracking
- User invitation system
- Role-based permissions
- Company multi-tenancy

## Installation

```bash
# Clone repository
git clone https://github.com/dkg8085/urlshortener.git
cd url-shortener-app

# Install dependencies
composer install
npm install && npm run build

# Configure environment
cp .env.example .env
php artisan key:generate

# Update .env with database credentials

# Run migrations and seeders
php artisan migrate --seed

# Start server
php artisan serve