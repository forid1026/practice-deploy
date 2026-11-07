# Laravel 13 Deployment Guide — Hostinger Shared Hosting

> Complete deployment documentation for Laravel 13 using GitHub, Hostinger SSH, shared storage, environment configuration, release-based deployment, and rollback.
>
> **Project:** Practice Laravel Deployment  
> **Domain:** `practice.nebedge.com`  
> **Repository:** `https://github.com/forid1026/practice-deploy`  
> **Hosting:** Hostinger Shared Hosting  
> **PHP:** 8.3  
> **Laravel:** 13  
> **Deployment Strategy:** Release-based deployment with shared `.env` and storage

---

## Table of Contents

- [1. Deployment Overview](#1-deployment-overview)
- [2. Server Information](#2-server-information)
- [3. Directory Structure](#3-directory-structure)
- [4. Required Tools](#4-required-tools)
- [5. First Deployment](#5-first-deployment)
- [6. Shared Environment Configuration](#6-shared-environment-configuration)
- [7. Shared Storage Setup](#7-shared-storage-setup)
- [8. Composer Installation](#8-composer-installation)
- [9. Laravel APP_KEY](#9-laravel-app_key)
- [10. Current Release Symlink](#10-current-release-symlink)
- [11. Subdomain Document Root](#11-subdomain-document-root)
- [12. Custom Laravel Entry Point](#12-custom-laravel-entry-point)
- [13. Laravel Cache and Database Verification](#13-laravel-cache-and-database-verification)
- [14. Browser Testing](#14-browser-testing)
- [15. Future Deployment](#15-future-deployment)
- [16. Rollback](#16-rollback)
- [17. Database Migration](#17-database-migration)
- [18. Storage and Upload Management](#18-storage-and-upload-management)
- [19. Troubleshooting](#19-troubleshooting)
- [20. Deployment Safety Checklist](#20-deployment-safety-checklist)
- [21. Important Commands Reference](#21-important-commands-reference)

---

# 1. Deployment Overview

This deployment architecture separates application releases from persistent data.

### Architecture

```text
GitHub Repository
       |
       v
Hostinger SSH
       |
       v
New Release Directory
       |
       +------ Shared .env
       |
       +------ Shared Storage
       |
       +------ Composer Dependencies
       |
       v
Update Current Symlink
       |
       v
Subdomain Document Root
       |
       v
Laravel Application
       |
       v
practice.nebedge.com
```

### Why use release-based deployment?

- Every deployment gets a separate release directory.
- Environment configuration remains persistent.
- Uploaded files survive deployment.
- Previous releases remain available for rollback.
- Deployment does not require modifying the main website.
- The active application is controlled by a single symlink.

---

# 2. Server Information

| Configuration | Value |
|---|---|
| SSH Username | `u444973435` |
| SSH Host | `us-bos-web1536` |
| Home Directory | `/home/u444973435` |
| Main Domain | `nebedge.com` |
| Subdomain | `practice.nebedge.com` |
| Document Root | `~/domains/nebedge.com/public_html/practice_project` |
| GitHub Repository | `forid1026/practice-deploy` |
| PHP CLI | 8.3.36 |
| Composer | 2.9.8 |
| Laravel | 13 |
| Database | MySQL |

### PHP Binary

```bash
/opt/alt/php83/usr/bin/php
```

Check PHP:

```bash
/opt/alt/php83/usr/bin/php -v
```

Check Composer:

```bash
composer --version
```

---

# 3. Directory Structure

The deployment uses three primary directories:

```text
/home/u444973435/

├── apps/
│   └── practice-app/
│       └── current -> active release
│
├── shared/
│   └── practice-app/
│       ├── .env
│       └── storage/
│
├── releases/
│   └── practice-app/
│       ├── 20261106-01/
│       ├── 20261120-01/
│       └── future-releases/
│
└── domains/
    └── nebedge.com/
        └── public_html/
            └── practice_project/
                ├── index.php
                ├── .htaccess
                ├── robots.txt
                ├── favicon.ico
                └── storage -> shared public storage
```

### Directory purposes

| Directory | Purpose |
|---|---|
| `apps/practice-app/current` | Active Laravel release |
| `shared/practice-app/.env` | Persistent environment configuration |
| `shared/practice-app/storage` | Persistent application storage |
| `releases/practice-app/` | All application releases |
| `practice_project/` | Subdomain public document root |

---

# 4. Required Tools

The following are required:

- Hostinger SSH access
- Git
- PHP 8.3
- Composer
- MySQL database
- GitHub repository

Check Git:

```bash
git --version
```

Check PHP extensions:

```bash
php -m | grep -Ei 'PDO|pdo_mysql'
```

Expected:

```text
PDO
pdo_mysql
```

> Node.js and npm are not required for Laravel's default welcome page. If the project uses Vite assets, frontend assets must be built locally or through CI/CD.

---

# 5. First Deployment

## Step 5.1 — Connect to SSH

From Windows Terminal or PowerShell:

```bash
ssh u444973435@us-bos-web1536
```

Verify home directory:

```bash
cd ~

pwd
```

Expected:

```text
/home/u444973435
```

## Step 5.2 — Create Deployment Directories

```bash
mkdir -p ~/apps/practice-app

mkdir -p ~/shared/practice-app

mkdir -p ~/releases/practice-app
```

Verify:

```bash
ls -la ~/apps

ls -la ~/shared

ls -la ~/releases
```

## Step 5.3 — Clone GitHub Repository

```bash
cd ~/releases/practice-app

git clone https://github.com/forid1026/practice-deploy.git 20261106-01
```

Enter release directory:

```bash
cd ~/releases/practice-app/20261106-01
```

Check files:

```bash
ls -la
```

---

# 6. Shared Environment Configuration

## Step 6.1 — Create Shared `.env`

```bash
cp ~/releases/practice-app/20261106-01/.env.example \
~/shared/practice-app/.env
```

## Step 6.2 — Edit `.env`

```bash
nano ~/shared/practice-app/.env
```

Configure production environment:

```env
APP_NAME=Laravel
APP_ENV=production
APP_DEBUG=false
APP_URL=https://practice.nebedge.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=YOUR_DATABASE_NAME
DB_USERNAME=YOUR_DATABASE_USERNAME
DB_PASSWORD=YOUR_DATABASE_PASSWORD
```

Replace database placeholders with actual credentials from Hostinger hPanel.

### Recommended configuration for a simple application

If database-backed sessions, cache, and queue are not required yet:

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

For database-backed sessions, cache, or queues, the corresponding database tables must exist.

## Step 6.3 — Protect `.env`

```bash
chmod 600 ~/shared/practice-app/.env
```

Verify:

```bash
ls -la ~/shared/practice-app
```

> Never commit `.env` to GitHub. Never share database passwords or APP_KEY publicly.

---

# 7. Shared Storage Setup

## Step 7.1 — Create Shared Storage Directories

```bash
mkdir -p ~/shared/practice-app/storage/app/private

mkdir -p ~/shared/practice-app/storage/app/public

mkdir -p ~/shared/practice-app/storage/framework/cache/data

mkdir -p ~/shared/practice-app/storage/framework/sessions

mkdir -p ~/shared/practice-app/storage/framework/views

mkdir -p ~/shared/practice-app/storage/logs
```

## Step 7.2 — Link Shared `.env`

Enter the release:

```bash
cd ~/releases/practice-app/20261106-01
```

Create symlink:

```bash
ln -s ~/shared/practice-app/.env .env
```

## Step 7.3 — Link Shared Storage

Preserve the original storage directory:

```bash
mv storage storage.initial
```

Create shared storage link:

```bash
ln -s ~/shared/practice-app/storage storage
```

Verify:

```bash
ls -la
```

Expected:

```text
.env -> /home/u444973435/shared/practice-app/.env

storage -> /home/u444973435/shared/practice-app/storage
```

---

# 8. Composer Installation

Enter release:

```bash
cd ~/releases/practice-app/20261106-01
```

Install production dependencies:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

Check:

```bash
ls -la vendor
```

### Important

Use:

```bash
composer install
```

Do not use:

```bash
composer update
```

during production deployment unless intentionally updating dependencies.

---

# 9. Laravel APP_KEY

Generate application key only during initial setup:

```bash
cd ~/releases/practice-app/20261106-01

php artisan key:generate
```

Verify:

```bash
grep -q '^APP_KEY=base64:' ~/shared/practice-app/.env \
&& echo "APP_KEY configured successfully"
```

### Important

- Keep APP_KEY persistent.
- Never regenerate it during routine deployments.
- Changing APP_KEY can invalidate sessions and encrypted application data.

---

# 10. Current Release Symlink

Create active release symlink:

```bash
ln -s ~/releases/practice-app/20261106-01 \
~/apps/practice-app/current
```

Verify:

```bash
ls -la ~/apps/practice-app
```

Expected:

```text
current -> /home/u444973435/releases/practice-app/20261106-01
```

Application stable path:

```text
/home/u444973435/apps/practice-app/current
```

---

# 11. Subdomain Document Root

Current subdomain document root:

```text
/home/u444973435/domains/nebedge.com/public_html/practice_project
```

Enter:

```bash
cd ~/domains/nebedge.com/public_html/practice_project
```

## Step 11.1 — Copy Laravel Public Files

```bash
cp ~/apps/practice-app/current/public/.htaccess .htaccess

cp ~/apps/practice-app/current/public/robots.txt robots.txt

cp ~/apps/practice-app/current/public/favicon.ico favicon.ico
```

## Step 11.2 — Public Storage Symlink

```bash
ln -s ~/shared/practice-app/storage/app/public storage
```

Verify:

```bash
ls -la
```

Expected:

```text
storage -> /home/u444973435/shared/practice-app/storage/app/public
```

## Step 11.3 — Backup Existing Hostinger File

Before replacing any existing default page, inspect:

```bash
ls -la default.php*
```

If `default.php` exists:

```bash
mv default.php default.php.backup
```

Do not delete the backup until the deployment is verified.

---

# 12. Custom Laravel Entry Point

Laravel's application code is outside the public document root.

Create:

```bash
nano index.php
```

Use the following code:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = '/home/u444973435/apps/practice-app/current';

if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

Save the file.

## Step 12.1 — Verify PHP Syntax

```bash
php -l index.php
```

Expected:

```text
No syntax errors detected in index.php
```

## Step 12.2 — Verify `.htaccess`

The Laravel `.htaccess` should include rewrite rules similar to:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-TOKEN}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

# 13. Laravel Cache and Database Verification

Enter active application:

```bash
cd ~/apps/practice-app/current
```

Clear cached configuration:

```bash
php artisan optimize:clear
```

Check routes:

```bash
php artisan route:list
```

Check database:

```bash
php artisan db:show
```

Check migration status:

```bash
php artisan migrate:status
```

### Migration table not found

If Laravel reports:

```text
Migration table not found.
```

The migration table has not been created yet.

This is normal for a fresh database before migrations.

The default Laravel welcome page does not require database migrations.

---

# 14. Browser Testing

Open:

```text
https://practice.nebedge.com
```

Verify:

- Laravel welcome page loads.
- HTTPS works.
- No 500 error.
- Subdomain points to correct document root.
- Main domain remains unaffected.

If the application fails, check logs:

```bash
tail -n 100 ~/shared/practice-app/storage/logs/laravel.log
```

---

# 15. Future Deployment

This is the most important reusable section.

For every new deployment, create a new release directory.

## Step 15.1 — Clone New Release

Example release:

```text
20261120-01
```

Commands:

```bash
cd ~/releases/practice-app

git clone https://github.com/forid1026/practice-deploy.git 20261120-01
```

Enter new release:

```bash
cd ~/releases/practice-app/20261120-01
```

## Step 15.2 — Link Shared `.env`

```bash
ln -s ~/shared/practice-app/.env .env
```

## Step 15.3 — Link Shared Storage

```bash
mv storage storage.initial

ln -s ~/shared/practice-app/storage storage
```

## Step 15.4 — Install Composer Dependencies

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

## Step 15.5 — Run Required Database Migrations

Only if the application includes new migrations:

```bash
php artisan migrate --force
```

Review migrations and backup the database before production migrations.

## Step 15.6 — Switch Current Release

```bash
ln -sfn /home/u444973435/releases/practice-app/20261120-01 \
/home/u444973435/apps/practice-app/current
```

## Step 15.7 — Clear Laravel Cache

```bash
cd ~/apps/practice-app/current

php artisan optimize:clear
```

## Step 15.8 — Verify

```bash
php artisan route:list
```

Then test:

```text
https://practice.nebedge.com
```

### Deployment flow

```text
Create Release
      |
      v
Clone GitHub
      |
      v
Link Shared .env
      |
      v
Link Shared Storage
      |
      v
Composer Install
      |
      v
Database Migration (if required)
      |
      v
Switch Current Symlink
      |
      v
Clear Cache
      |
      v
Browser Testing
```

---

# 16. Rollback

If a new deployment fails, switch back to the previous release.

Example previous release:

```text
20261106-01
```

Rollback:

```bash
ln -sfn /home/u444973435/releases/practice-app/20261106-01 \
/home/u444973435/apps/practice-app/current
```

Clear cache:

```bash
cd ~/apps/practice-app/current

php artisan optimize:clear
```

Test:

```text
https://practice.nebedge.com
```

### Important

Rollback restores application code, not database changes.

If a migration changed the database incompatibly, restoring the previous code may not be enough.

---

# 17. Database Migration

For a fresh Laravel project:

```bash
cd ~/apps/practice-app/current

php artisan migrate --force
```

Before production migration:

1. Backup database.
2. Review migration files.
3. Confirm database credentials.
4. Ensure PHP extensions are available.
5. Run migration.
6. Verify migration status.

Check:

```bash
php artisan migrate:status
```

### Never blindly run

```bash
php artisan migrate:fresh --force
```

This command drops existing database tables and is dangerous for production.

---

# 18. Storage and Upload Management

Shared storage path:

```text
/home/u444973435/shared/practice-app/storage
```

Public upload directory:

```text
/home/u444973435/shared/practice-app/storage/app/public
```

Laravel public storage link:

```text
/home/u444973435/domains/nebedge.com/public_html/practice_project/storage
```

This link points to:

```text
/home/u444973435/shared/practice-app/storage/app/public
```

### Benefits

- Uploaded files survive deployments.
- Files remain independent of release directories.
- Old releases can be deleted without deleting uploads.
- Application storage remains outside the public document root.

### Important

Do not delete shared storage during deployment.

Do not run destructive commands such as:

```bash
rm -rf ~/shared/practice-app/storage
```

---

# 19. Troubleshooting

## 19.1 — 500 Internal Server Error

Check:

```bash
tail -n 100 ~/shared/practice-app/storage/logs/laravel.log
```

Clear cache:

```bash
cd ~/apps/practice-app/current

php artisan optimize:clear
```

Verify:

```bash
php -l ~/domains/nebedge.com/public_html/practice_project/index.php
```

## 19.2 — Database Connection Error

Check:

```bash
php artisan db:show
```

Verify `.env` values:

```bash
grep -E '^(DB_CONNECTION|DB_HOST|DB_PORT|DB_DATABASE)=' \
~/shared/practice-app/.env
```

Do not display or share database passwords.

## 19.3 — Migration Table Not Found

Run:

```bash
php artisan migrate:status
```

If no migration table exists and the application requires database tables:

```bash
php artisan migrate --force
```

## 19.4 — Storage Files Not Showing

Check:

```bash
ls -la ~/shared/practice-app/storage/app/public
```

Check symlink:

```bash
ls -la ~/domains/nebedge.com/public_html/practice_project
```

Expected:

```text
storage -> /home/u444973435/shared/practice-app/storage/app/public
```

## 19.5 — Composer Error

Check:

```bash
php -v

composer --version
```

Use:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

If the server has no Node.js, frontend assets must be built locally or through CI/CD.

## 19.6 — Permission Error

Inspect:

```bash
ls -la ~/shared/practice-app

ls -la ~/shared/practice-app/storage

ls -la ~/shared/practice-app/storage/framework

ls -la ~/shared/practice-app/storage/logs
```

Laravel requires writable storage and bootstrap cache directories.

---

# 20. Deployment Safety Checklist

## First Deployment

- [ ] SSH connection verified
- [ ] PHP 8.3 verified
- [ ] Composer verified
- [ ] GitHub repository cloned
- [ ] Shared directories created
- [ ] Shared `.env` configured
- [ ] Database credentials verified
- [ ] APP_KEY generated
- [ ] Shared storage linked
- [ ] Composer dependencies installed
- [ ] Current symlink created
- [ ] Subdomain document root configured
- [ ] Custom `index.php` created
- [ ] `.htaccess` verified
- [ ] PHP syntax verified
- [ ] Laravel cache cleared
- [ ] Browser tested

## Future Deployment

- [ ] New release created
- [ ] Shared `.env` linked
- [ ] Shared storage linked
- [ ] Composer install completed
- [ ] Migration reviewed
- [ ] Database backup taken if required
- [ ] Current symlink switched
- [ ] Cache cleared
- [ ] Browser tested
- [ ] Old release retained

## Production Safety

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `.env` is not publicly accessible
- [ ] APP_KEY remains unchanged
- [ ] Shared storage preserved
- [ ] Main domain untouched
- [ ] Database backup available
- [ ] No `migrate:fresh` in production
- [ ] No unnecessary `composer update`
- [ ] No destructive storage deletion

---

# 21. Important Commands Reference

| Command | Purpose |
|---|---|
| `pwd` | Show current directory |
| `ls -la` | Show files and symlinks |
| `php -v` | Check PHP version |
| `composer --version` | Check Composer |
| `git --version` | Check Git |
| `composer install --no-dev --optimize-autoloader` | Install production dependencies |
| `php artisan key:generate` | Generate APP_KEY |
| `php artisan optimize:clear` | Clear Laravel caches |
| `php artisan route:list` | Show routes |
| `php artisan db:show` | Check database |
| `php artisan migrate:status` | Check migration status |
| `php artisan migrate --force` | Run production migrations |
| `php -l index.php` | Validate PHP syntax |
| `ln -s` | Create symlink |
| `ln -sfn` | Update symlink |
| `tail -n 100 laravel.log` | Inspect Laravel logs |
| `chmod 600 .env` | Restrict environment file permissions |

---

# Final Notes

### Deployment Architecture

```text
GitHub
  |
  v
Hostinger SSH
  |
  v
~/releases/practice-app/
  |
  +-- Release 1
  +-- Release 2
  +-- Release 3
  |
  v
~/apps/practice-app/current
  |
  v
practice_project/index.php
  |
  v
Laravel Application
  |
  v
practice.nebedge.com
```

### Persistent Data

```text
~/shared/practice-app/.env

~/shared/practice-app/storage
```

These directories must remain persistent across deployments.

### Core Deployment Rule

> Deploy application code into a new release directory, reuse shared environment and storage, switch the current symlink, clear cache, and verify the application.

---

**Document:** Laravel 13 Hostinger Deployment Manual  
**Repository:** https://github.com/forid1026/practice-deploy  
**Domain:** https://practice.nebedge.com  
**Maintained for:** Future Laravel deployment reference
