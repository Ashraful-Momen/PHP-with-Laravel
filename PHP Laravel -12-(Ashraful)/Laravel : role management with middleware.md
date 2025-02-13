To add user roles with login and registration functionality in Laravel, follow these steps to set up user roles (`admin` and `user`), and implement login and registration. 

### 1. **Set Up Authentication**

Laravel provides built-in authentication scaffolding. If you haven’t already set it up, you can use Laravel Breeze, Laravel UI, or Laravel Fortify for authentication.

#### Using Laravel Breeze (recommended for simplicity)

1. **Install Laravel Breeze**:
   
   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install
   npm install && npm run dev
   php artisan migrate
   ```

   This sets up basic authentication (login and registration) with a simple UI.

### 2. **Add User Role to Database**

You need to modify the users table to include roles. We already covered this in the previous setup.

**Create Migration:**

```bash
php artisan make:migration add_role_to_users_table --table=users
```

**Update Migration File:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
```

**Run the Migration:**

```bash
php artisan migrate
```

### 3. **Update Registration to Include Role**

Modify the registration process to allow setting the user role.

**Update Registration Controller:**

Find `app/Http/Controllers/Auth/RegisteredUserController.php` and modify the `store` method to include the role:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    use RegistersUsers;

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user', // Set default role as 'user'
        ]);
    }
}
```

**Update Registration Blade View:**

Update the registration view `resources/views/auth/register.blade.php` to include a role selection:

```html
<div>
    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>
</div>
```

### 4. **Middleware for Role-Based Access**

Create middleware to restrict access based on user roles.

**Create Middleware:**

```bash
php artisan make:middleware RoleMiddleware
```

**Update Middleware (`app/Http/Middleware/RoleMiddleware.php`):**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }

        return redirect('/'); // Redirect if the user does not have the required role
    }
}
```

**Register Middleware:**

In `app/Http/Kernel.php`, register the middleware:

```php
protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

### 5. **Controllers with Middleware**

**Admin Controller (`app/Http/Controllers/AdminController.php`):**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index()
    {
        return view('admin.index');
    }
}
```

**User Controller (`app/Http/Controllers/UserController.php`):**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:user');
    }

    public function index()
    {
        return view('user.index');
    }
}
```

### 6. **Define Routes**

In `routes/web.php`, set up routes:

```php
// Admin routes
Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');

// User routes
Route::get('/user', [App\Http\Controllers\UserController::class, 'index'])->name('user.index');
```

### 7. **Create Views**

Create views for each role.

**Admin View (`resources/views/admin/index.blade.php`):**

```html
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Admin</h1>
</body>
</html>
```

**User View (`resources/views/user/index.blade.php`):**

```html
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome, User</h1>
</body>
</html>
```

### Summary

1. **Authentication**: Use Laravel Breeze for basic login and registration.
2. **Database Migration**: Add a `role` column to the `users` table.
3. **Registration**: Modify the registration to include role selection.
4. **Middleware**: Create middleware to handle role-based access.
5. **Controllers**: Set up controllers with role-based access.
6. **Routes**: Define routes for different roles.
7. **Views**: Create views for admin and user dashboards.

This setup will allow you to manage user roles and restrict access to different parts of your application based on the user's role.
