To implement role-based permissions for the product CRUD operations in your Laravel project:

### Step 1: Define Middleware for Permissions
Create middleware for admin, manager, and user roles:

```bash
php artisan make:middleware AdminMiddleware
php artisan make:middleware ManagerMiddleware
php artisan make:middleware UserMiddleware
```

#### `app/Http/Middleware/AdminMiddleware.php`
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
```

#### `app/Http/Middleware/ManagerMiddleware.php`
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ManagerMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'manager') {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
```

#### `app/Http/Middleware/UserMiddleware.php`
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'user') {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
```

### Step 2: Register Middleware
In `app/Http/Kernel.php`, register the middleware:

```php
protected $routeMiddleware = [
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'manager' => \App\Http\Middleware\ManagerMiddleware::class,
    'user' => \App\Http\Middleware\UserMiddleware::class,
];
```

### Step 3: Define Routes
Group routes with the appropriate middleware for roles:

#### `routes/web.php`
```php
use App\Http\Controllers\ProductController;

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
});

// Manager routes
Route::middleware(['auth', 'manager'])->group(function () {
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
});

// User routes
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
});
```

### Step 4: Modify the Product Controller
In `ProductController`, implement authorization checks if needed for actions:

```php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
```

### Step 5: Test Your Setup
1. **Admin**: Can access all routes (`index`, `show`, `store`, `update`, `destroy`).
2. **Manager**: Can only access `store` and `update`.
3. **User**: Can only access `index` and `show`.

Let me know if you need further assistance!
