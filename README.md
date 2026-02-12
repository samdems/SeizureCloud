"# SeizureCloud

A comprehensive epilepsy diary and seizure tracking application built with Laravel.

## Quick Start

```bash
# Install dependencies
vendor/bin/sail composer install
vendor/bin/sail npm install

# Run migrations
vendor/bin/sail artisan migrate

# Start development
vendor/bin/sail composer run dev
```

## 🚨 Storage Permission Issues?

If you get permission errors, run:
```bash
./fix-storage-permissions.sh
```

See [STORAGE_PERMISSIONS.md](STORAGE_PERMISSIONS.md) for details.

## Development Commands

Always use Sail for Laravel commands:
```bash
# Artisan
vendor/bin/sail artisan [command]

# Testing  
vendor/bin/sail artisan test

# Composer
vendor/bin/sail composer [command]
```" 
