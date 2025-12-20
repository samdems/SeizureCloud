# Status Page Setup Guide

## Overview
The status page provides real-time monitoring of your Laravel application's health, including database connectivity, queue system status, cache functionality, and more.

## Current Setup

### ✅ What's Working Now
- **Minimal Status Page**: `/admin/status` - Basic health checks without complex dependencies
- **System Information**: PHP version, Laravel version, memory usage
- **Database Connection**: Tests database connectivity
- **Cache System**: Tests cache read/write operations
- **Basic Queue Info**: Shows queue configuration

### 🚧 What Needs Setup

#### 1. Queue Tables (Required for Full Queue Monitoring)
The application uses Laravel's queue system for background jobs like medication reminders. To enable full queue monitoring:

```bash
# Run this command in your project directory
php artisan migrate
```

This will create the `jobs` and `failed_jobs` tables needed for queue monitoring.

#### 2. Queue Worker (Recommended)
To process background jobs, you need to run a queue worker:

```bash
# For development (run this in a separate terminal)
php artisan queue:work

# For production (use a process manager like Supervisor)
php artisan queue:work --daemon
```

#### 3. Task Scheduler (Optional but Recommended)
To run health checks automatically every minute:

```bash
# Add this to your system's crontab (Linux/Mac) or Task Scheduler (Windows)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Available Pages

### 1. Minimal Status Page
- **URL**: `/admin/status`
- **Purpose**: Basic health monitoring without dependencies
- **Features**: 
  - PHP status
  - Database connectivity
  - Cache functionality
  - Memory usage
  - Basic system info

### 2. Full Status Page (After Setup)
- **URL**: `/admin/status-full`
- **Purpose**: Complete health monitoring with all features
- **Features**:
  - All minimal features
  - Queue job statistics
  - Failed job monitoring
  - Automated health checks
  - Real-time updates

### 3. API Endpoint
- **URL**: `/admin/api/status`
- **Purpose**: JSON API for external monitoring
- **Response**: Complete system status in JSON format

### 4. Debug Endpoint
- **URL**: `/admin/debug/status`
- **Purpose**: Troubleshooting system issues
- **Response**: Detailed diagnostic information

## Troubleshooting

### Status Page Won't Load
1. Check if you have admin access
2. Ensure database connection is working
3. Try the minimal status page first: `/admin/status`
4. Check debug endpoint: `/admin/debug/status`

### Queue Monitoring Shows "Setup Needed"
1. Run migrations: `php artisan migrate`
2. Start queue worker: `php artisan queue:work`
3. Refresh the status page

### Health Checks Not Running
1. Ensure task scheduler is set up (see step 3 above)
2. Check if queue worker is running
3. Manually trigger: `php artisan health:check --sync`

## Features

### Real-time Monitoring
- **Database**: Connection status and query latency
- **Queue System**: Pending jobs, failed jobs, success rate
- **Cache**: Read/write functionality test
- **Storage**: File system accessibility
- **Memory**: Current usage and limits
- **Scheduler**: Last run time and status

### Visual Indicators
- 🟢 **Green**: System healthy and operational
- 🟡 **Yellow**: System working but needs attention
- 🔴 **Red**: System error or offline
- 🔵 **Blue**: System checking or unknown status

### Auto-refresh
- Status page automatically refreshes every 30 seconds (when enabled)
- Health checks run every minute via scheduled jobs
- Manual refresh button available

## Commands

### Available Artisan Commands
```bash
# Run health check manually (synchronous)
php artisan health:check --sync

# Run health check in background (queued)
php artisan health:check

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

## Integration

### External Monitoring
Use the API endpoint (`/admin/api/status`) to integrate with:
- Uptime monitoring services
- Custom dashboards
- Mobile applications
- Automated alerts

### Example API Response
```json
{
  "status": "healthy",
  "message": "All systems operational",
  "timestamp": "2024-01-15T10:30:00Z",
  "services": {
    "database": {
      "status": "healthy",
      "message": "Database connection successful",
      "latency_ms": 12.5
    },
    "queue": {
      "status": "healthy",
      "pending_jobs": 3,
      "failed_jobs": 0
    }
  },
  "system": {
    "php_version": "8.2.0",
    "laravel_version": "10.x",
    "environment": "production"
  }
}
```

## Next Steps

1. **Immediate**: Use the minimal status page (`/admin/status`) to monitor basic health
2. **Short-term**: Run `php artisan migrate` to enable queue monitoring
3. **Long-term**: Set up task scheduler and queue workers for full automation

## Support

If you encounter issues:
1. Check the debug endpoint: `/admin/debug/status`
2. Review Laravel logs: `storage/logs/laravel.log`
3. Ensure all dependencies are installed: `composer install`
4. Verify database configuration in `.env` file

The status page is designed to help you maintain a healthy application by providing visibility into all critical systems.