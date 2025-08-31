The 419 "Page Expired" error combined with IP-specific access issues indicates CSRF token and session configuration problems in your dockerized Laravel app. Here are the solutions:

## Issue 1: CSRF Token Configuration

**Check your `.env` file:**
```env
# Make sure these are set correctly
APP_URL=http://your-vps-ip-or-domain
APP_ENV=production
APP_DEBUG=false

# Session configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_DOMAIN=null
SESSION_SECURE_COOKIES=false
SESSION_HTTPONLY=true
SESSION_SAME_SITE=lax

# Redis connection (if using Redis for sessions)
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Issue 2: Trusted Proxies (Most Likely Cause)

Since you're running in Docker on a VPS, Laravel needs to trust the proxy. 

**Edit `app/Http/Middleware/TrustProxies.php`:**
```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     */
    protected $proxies = [
        '172.16.0.0/12',  // Docker internal network
        '192.168.0.0/16', // Docker internal network
        '10.0.0.0/8',     // Docker internal network
        '*'               // Trust all proxies (use with caution)
    ];

    /**
     * The headers that should be used to detect proxies.
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR | 
                         Request::HEADER_X_FORWARDED_HOST | 
                         Request::HEADER_X_FORWARDED_PORT | 
                         Request::HEADER_X_FORWARDED_PROTO | 
                         Request::HEADER_X_FORWARDED_AWS_ELB;
}
```

## Issue 3: CORS Configuration

**Update `config/cors.php`:**
```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Or specify your domains
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

## Issue 4: Session Configuration

**Update `config/session.php`:**
```php
// Make sure these settings are correct
'domain' => env('SESSION_DOMAIN', null), // Keep as null for IP access
'secure' => env('SESSION_SECURE_COOKIES', false), // Set to false for HTTP
'http_only' => env('SESSION_HTTP_ONLY', true),
'same_site' => env('SESSION_SAME_SITE', 'lax'),
```

## Issue 5: Nginx Configuration

**Update your `docker/nginx/default.conf`:**
```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php index.html index.htm;

    # Important: Pass real IP to PHP
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        
        # Pass real client IP
        fastcgi_param HTTP_X_REAL_IP $remote_addr;
        fastcgi_param HTTP_X_FORWARDED_FOR $proxy_add_x_forwarded_for;
        fastcgi_param HTTP_X_FORWARDED_PROTO $scheme;
        fastcgi_param HTTP_X_FORWARDED_HOST $host;
        
        include fastcgi_params;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
}
```

## Issue 6: Laravel Configuration Cache

**Clear and recache configuration:**
```bash
# Inside your Laravel app container
docker exec -it laravel_app bash

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Recache for production
php artisan config:cache
php artisan route:cache
```

## Issue 7: VPS Firewall Settings

**Check your VPS firewall:**
```bash
# Check if ports are open
sudo ufw status

# Allow HTTP and HTTPS if needed
sudo ufw allow 80
sudo ufw allow 443
sudo ufw allow 8081  # For phpMyAdmin if needed

# Restart firewall
sudo ufw reload
```

## Complete Fix Script

Create this script to apply all fixes:

```bash
#!/bin/bash
echo "Fixing Laravel CSRF and IP access issues..."

# 1. Restart containers
docker-compose down
docker-compose up -d

# 2. Wait for containers to be ready
sleep 30

# 3. Clear Laravel caches
docker exec -it laravel_app php artisan config:clear
docker exec -it laravel_app php artisan cache:clear
docker exec -it laravel_app php artisan route:clear
docker exec -it laravel_app php artisan view:clear

# 4. Generate new app key if needed
docker exec -it laravel_app php artisan key:generate

# 5. Cache configuration
docker exec -it laravel_app php artisan config:cache

echo "Fix applied. Test from external IP now."
```

## Testing Steps

1. **Apply the TrustProxies fix first** (most important)
2. Update your `.env` with correct `APP_URL`
3. Clear all caches
4. Test from external IP
5. Check browser developer tools for CSRF token in forms
6. Monitor Laravel logs: `docker exec -it laravel_app tail -f storage/logs/laravel.log`

The most common cause is the **TrustProxies middleware** not being configured for Docker networks. This prevents Laravel from properly detecting the real client IP and causes CSRF token mismatches.
