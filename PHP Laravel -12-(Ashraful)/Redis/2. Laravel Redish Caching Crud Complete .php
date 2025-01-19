<?php

// 1. First, install Redis and the Laravel Redis package
// composer require predis/predis

// 2. Configure Redis in config/database.php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
    ],
],

// 3. Create a Product Model
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'description'];
    
    // Define cache key prefix
    private static $cachePrefix = 'product:';
    
    // Boot function for automatic cache updates
    protected static function boot()
    {
        parent::boot();
        
        // After creating a product
        static::created(function ($product) {
            // Clear list cache
            Redis::del('products.all');
            // Cache new product
            Redis::set(self::$cachePrefix . $product->id, json_encode($product));
        });
        
        // After updating a product
        static::updated(function ($product) {
            // Clear list cache
            Redis::del('products.all');
            // Update product cache
            Redis::set(self::$cachePrefix . $product->id, json_encode($product));
        });
        
        // Before deleting a product
        static::deleted(function ($product) {
            // Clear list cache
            Redis::del('products.all');
            // Remove product from cache
            Redis::del(self::$cachePrefix . $product->id);
        });
    }
    
    // Helper method to get cache key
    public static function getCacheKey($id)
    {
        return self::$cachePrefix . $id;
    }
}

// 4. Create ProductController
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ProductController extends Controller
{
    // List all products with cache
    public function index()
    {
        // Try to get from cache first
        $cachedProducts = Redis::get('products.all');
        
        if ($cachedProducts) {
            return response()->json(json_decode($cachedProducts));
        }
        
        // If not in cache, get from database and cache it
        $products = Product::all();
        Redis::set('products.all', json_encode($products));
        
        return response()->json($products);
    }
    
    // Show single product with cache
    public function show($id)
    {
        // Try to get from cache first
        $cachedProduct = Redis::get(Product::getCacheKey($id));
        
        if ($cachedProduct) {
            return response()->json(json_decode($cachedProduct));
        }
        
        // If not in cache, get from database and cache it
        $product = Product::findOrFail($id);
        Redis::set(Product::getCacheKey($id), json_encode($product));
        
        return response()->json($product);
    }
    
    // Store new product
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'required'
        ]);
        
        $product = Product::create($validatedData);
        // Cache will be automatically updated by boot() method
        
        return response()->json($product, 201);
    }
    
    // Update product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'required'
        ]);
        
        $product->update($validatedData);
        // Cache will be automatically updated by boot() method
        
        return response()->json($product);
    }
    
    // Delete product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        // Cache will be automatically updated by boot() method
        
        return response()->json(null, 204);
    }
}

// 5. Add routes in routes/api.php
use App\Http\Controllers\ProductController;

Route::apiResource('products', ProductController::class);

// 6. Create migration for products table
// php artisan make:migration create_products_table

public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 10, 2);
        $table->text('description');
        $table->timestamps();
    });
}

// Usage Examples:

// 1. Create a new product
$response = Http::post('/api/products', [
    'name' => 'New Product',
    'price' => 99.99,
    'description' => 'Product description'
]);

// 2. Get all products
$products = Http::get('/api/products');

// 3. Get single product
$product = Http::get('/api/products/1');

// 4. Update product
$response = Http::put('/api/products/1', [
    'name' => 'Updated Product',
    'price' => 149.99,
    'description' => 'Updated description'
]);

// 5. Delete product
$response = Http::delete('/api/products/1');
