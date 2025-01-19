# Laravel Redis API Tutorial
## Prerequisites
- Laravel 10.x
- Redis server installed
- Basic understanding of Laravel

## 1. Project Setup

First, create a new Laravel project and install required packages:

```bash
composer create-project laravel/laravel redis-api
cd redis-api
composer require predis/predis
```

Update your `.env` file:
```env
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
QUEUE_CONNECTION=redis
```

## 2. Create a Simple Product API

Let's create a product model and controller:

```bash
php artisan make:model Product -m
php artisan make:controller ProductController --api
```

Update the migration file (`database/migrations/xxxx_create_products_table.php`):
```php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 8, 2);
        $table->integer('stock');
        $table->timestamps();
    });
}
```

Run the migration:
```bash
php artisan migrate
```

## 3. Implement Rate Limiting

Update `app/Http/Kernel.php` to add a new middleware:

```php
protected $routeMiddleware = [
    // ... other middlewares
    'throttle.redis' => \App\Http\Middleware\RedisRateLimiting::class,
];
```

Create a new middleware:
```bash
php artisan make:middleware RedisRateLimiting
```

Update `app/Http/Middleware/RedisRateLimiting.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

class RedisRateLimiting
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $key = 'api_rate_limit:' . $ip;
        
        // Get current requests count
        $requests = Redis::get($key) ?? 0;
        
        if ($requests >= 60) { // 60 requests per minute limit
            return response()->json([
                'error' => 'Too many requests',
            ], 429);
        }
        
        // Increment requests and set expiry
        Redis::incr($key);
        Redis::expire($key, 60);
        
        return $next($request);
    }
}
```

## 4. Create Job for Product Processing

Create a new job:
```bash
php artisan make:job ProcessProduct
```

Update `app/Jobs/ProcessProduct.php`:
```php
<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productData;

    public function __construct(array $productData)
    {
        $this->productData = $productData;
    }

    public function handle()
    {
        Product::create($this->productData);
    }
}
```

## 5. Update ProductController

Update `app/Http/Controllers/ProductController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle.redis')->only(['store', 'bulkStore']);
    }

    // Get all products
    public function index()
    {
        return Product::all();
    }

    // Store single product
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer'
        ]);

        ProcessProduct::dispatch($validated);

        return response()->json(['message' => 'Product is being processed'], 202);
    }

    // Bulk store products
    public function bulkStore(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.name' => 'required|string',
            'products.*.price' => 'required|numeric',
            'products.*.stock' => 'required|integer'
        ]);

        $products = $request->products;
        
        // Using Redis pipeline for better performance
        Redis::pipeline(function ($pipe) use ($products) {
            foreach ($products as $product) {
                ProcessProduct::dispatch($product);
            }
        });

        return response()->json([
            'message' => count($products) . ' products are being processed'
        ], 202);
    }
}
```

## 6. Define Routes

Update `routes/api.php`:
```php
use App\Http\Controllers\ProductController;

Route::apiResource('products', ProductController::class);
Route::post('products/bulk', [ProductController::class, 'bulkStore']);
```

## 7. Start the Queue Worker

Run this command to start processing jobs:
```bash
php artisan queue:work redis
```

## Usage Examples

1. Create a single product:
```bash
curl -X POST http://your-app.test/api/products \
  -H "Content-Type: application/json" \
  -d '{"name":"Product 1","price":29.99,"stock":100}'
```

2. Bulk create products:
```bash
curl -X POST http://your-app.test/api/products/bulk \
  -H "Content-Type: application/json" \
  -d '{
    "products": [
      {"name":"Product 1","price":29.99,"stock":100},
      {"name":"Product 2","price":39.99,"stock":50},
      {"name":"Product 3","price":19.99,"stock":200}
    ]
  }'
```

## Key Features Implemented

1. **Rate Limiting with Redis:**
   - IP-based rate limiting (60 requests per minute)
   - Uses Redis to track request counts
   - Automatically expires after 60 seconds

2. **Job Queue System:**
   - Asynchronous product creation
   - Redis queue driver
   - Scalable job processing

3. **High-Volume Data Processing:**
   - Bulk insertion support
   - Redis pipeline for better performance
   - Queue-based processing to handle large datasets

## Performance Tips

1. Always run queue worker in production:
```bash
php artisan queue:work redis --tries=3 --timeout=90
```

2. For supervisor configuration (production), create `/etc/supervisor/conf.d/laravel-worker.conf`:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
```

3. Monitor your Redis memory usage and configure appropriate maxmemory and eviction policies in `redis.conf`:
```conf
maxmemory 2gb
maxmemory-policy allkeys-lru
```
