# Laravel ORM & SQL Mastery Cheat Sheet (0 to Hero)

## 🎯 Table of Contents
1. [Basic Query Builder](#basic-query-builder)
2. [Joins & Relationships](#joins--relationships)
3. [Raw SQL & Complex Queries](#raw-sql--complex-queries)
4. [Conditional Logic](#conditional-logic)
5. [Aggregations & Grouping](#aggregations--grouping)
6. [Union Queries](#union-queries)
7. [Advanced Techniques](#advanced-techniques)
8. [Performance & Optimization](#performance--optimization)
9. [Database Schema Checks](#database-schema-checks)
10. [Common Patterns](#common-patterns)

---

## 🚀 Basic Query Builder

### Simple Selects
```php
// Basic select
DB::table('users')->get();
DB::table('users')->first();
DB::table('users')->find(1);

// Select specific columns
DB::table('users')->select('name', 'email')->get();
DB::table('users')->select(['name', 'email'])->get();

// Where clauses
DB::table('users')->where('active', 1)->get();
DB::table('users')->where('votes', '>', 100)->get();
DB::table('users')->whereBetween('votes', [1, 100])->get();
DB::table('users')->whereIn('id', [1, 2, 3])->get();
DB::table('users')->whereNull('email_verified_at')->get();
```

### Date Filtering
```php
// From your code - proper date formatting
if ($startDate) {
    $startDate = Carbon::createFromFormat('Y/m/d', $startDate)
        ->startOfDay()->toDateTimeString();
}
if ($endDate) {
    $endDate = Carbon::createFromFormat('Y/m/d', $endDate)
        ->endOfDay()->toDateTimeString();
}

// Apply date range
$query->whereBetween('created_at', [$startDate, $endDate]);
```

---

## 🔗 Joins & Relationships

### Basic Joins
```php
// Inner Join
DB::table('users')
    ->join('posts', 'users.id', '=', 'posts.user_id')
    ->get();

// Left Join
DB::table('users')
    ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
    ->get();

// Multiple conditions in join
DB::table('users')
    ->leftJoin('posts', function($join) {
        $join->on('users.id', '=', 'posts.user_id')
             ->where('posts.published', '=', 1);
    })
    ->get();
```

### Complex Joins from Your Code
```php
// Join with specific condition
->leftJoin('life_and_healths', 'life_and_health_child_orders.product_id', '=', 'life_and_healths.id')
->leftJoin('policy_providers', function ($join) {
    $join->on('life_and_healths.provider_name', '=', 'policy_providers.id');
})

// Join with raw value
->join("categories", function ($join) {
    $join->on(DB::raw("5"), "=", "categories.id");
})

// Conditional joins based on field values
->leftJoin('bike_insurance_admin_tables', function ($join) {
    $join->on('motor_order_children.pkg_id', '=', 'bike_insurance_admin_tables.id')
         ->where('motor_order_children.car_or_motor_insurance', '=', "'bike'");
})
```

---

## 🎯 Raw SQL & Complex Queries

### Basic Raw Expressions
```php
// Raw select
DB::raw("COUNT(*) as total")
DB::raw("SUM(amount) as total_amount")
DB::raw("CONCAT(first_name, ' ', last_name) as full_name")

// Raw where
->whereRaw('age > ?', [25])
->whereRaw('YEAR(created_at) = ?', [2024])
```

### Advanced Raw Queries from Your Code

#### Safe Column Existence Check
```php
// Check if column exists before using it
DB::raw("COALESCE(
    CASE
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns
            WHERE table_schema = DATABASE()
            AND table_name = 'life_and_health_child_orders'
            AND column_name = 'due_payment'
        )
        THEN COALESCE(life_and_health_child_orders.due_payment, 0)
        ELSE 0
    END, 0
) as due_amount")
```

#### Character Set Handling
```php
// Proper UTF-8 handling
DB::raw("CAST(COALESCE(dealers.com_org_inst_name, 'Instasure') AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as sold_by")
```

#### JSON Extraction
```php
// Extract from JSON fields
DB::raw("JSON_UNQUOTE(JSON_EXTRACT(device_insurances.customer_info, '$.customer_name')) as customer_name")
DB::raw("COALESCE(JSON_UNQUOTE(JSON_EXTRACT(travel_ins_orders.payment_details, '$.pgw_name')), 'Instasure') as payment_method")

// JSON conditions
->whereRaw("JSON_EXTRACT(travel_ins_orders.payment_details, '$.pgw_name') = ?", [$paymentMethod])
```

---

## 🔄 Conditional Logic

### CASE Statements
```php
// Simple CASE
DB::raw("CASE 
    WHEN status = 'active' THEN 'Active User'
    WHEN status = 'inactive' THEN 'Inactive User'
    ELSE 'Unknown'
END as status_label")

// Complex CASE from your code
DB::raw("CASE
    WHEN COALESCE(order_parents.total_payment, 0) > 0
    THEN COALESCE(order_parents.total_payment, 0)
    
    WHEN COALESCE(life_and_health_child_orders.total_payment, 0) > 0
    THEN COALESCE(life_and_health_child_orders.total_payment, 0)
    
    ELSE COALESCE(life_and_health_child_orders.due_payment, 0)
END as total_payment")
```

### Conditional WHERE Clauses
```php
// Multiple OR conditions
if ($provider) {
    $query->where(function ($q) use ($provider) {
        $q->where('policy_providers.id', '=', $provider)
          ->orWhere('life_and_healths.provider_name', '=', $provider);
    });
}

// Dynamic filtering
if ($paymentMethod) {
    $query->where('order_parents.pgw_name', '=', $paymentMethod);
}
if ($parentDealer) {
    $query->where('dealers.parent_id', '=', $parentDealer);
}
```

---

## 📊 Aggregations & Grouping

### Basic Aggregations
```php
// Count, Sum, Average
DB::table('orders')->count();
DB::table('orders')->sum('amount');
DB::table('orders')->avg('amount');
DB::table('orders')->max('amount');
DB::table('orders')->min('amount');

// Group by
DB::table('orders')
    ->select('status', DB::raw('COUNT(*) as count'))
    ->groupBy('status')
    ->get();
```

### Complex Calculations
```php
// Revenue calculation from your code
$vatAmount = ($order->total_amount * $order->vat) / 100;
$revenue = $order->total_amount - $vatAmount - $order->comission - $order->provider_amount;

// In query
DB::raw("(total_amount - (total_amount * vat / 100) - commission - provider_amount) as revenue")
```

---

## 🔗 Union Queries

### Basic Union
```php
$first = DB::table('users')->where('active', 1);
$second = DB::table('users')->where('premium', 1);

$users = $first->union($second)->get();
```

### Complex Union from Your Code
```php
// Multiple query union pattern
$queries = [];

// Add different category queries
if (in_array(8, $categoriesToInclude)) {
    $lifeHealthQuery = DB::table("order_parents")
        ->where("order_parents.category_id", "=", 8)
        // ... complex query setup
    $queries[] = $lifeHealthQuery;
}

if (in_array(5, $categoriesToInclude)) {
    $travelQuery = DB::table('travel_ins_orders')
        // ... another complex query
    $queries[] = $travelQuery;
}

// Combine all queries
$finalQuery = array_shift($queries);
foreach ($queries as $query) {
    $finalQuery->union($query);
}

return $finalQuery->get();
```

---

## 🚀 Advanced Techniques

### Dynamic Query Building
```php
public static function buildDynamicQuery($filters = []) {
    $query = DB::table('orders');
    
    // Dynamic category inclusion
    $categoriesToInclude = [];
    if ($filters['category'] == 100) { // All Reports
        $categoriesToInclude = [5, 6, 8, 10, 11, 12, 16, 17, 18, 20, 21];
    } elseif ($filters['category'] == 7) { // Dealer
        $categoriesToInclude = [5, 6, 8, 10, 11, 12, 16, 17, 18, 20, 21];
    } else {
        $categoriesToInclude = [$filters['category']];
    }
    
    return $query->whereIn('category_id', $categoriesToInclude);
}
```

### Subqueries
```php
// Subquery in SELECT
DB::table('users')
    ->select('name', DB::raw('(
        SELECT COUNT(*) FROM posts 
        WHERE posts.user_id = users.id
    ) as post_count'))
    ->get();

// Exists subquery
DB::table('users')
    ->whereExists(function ($query) {
        $query->select(DB::raw(1))
              ->from('orders')
              ->whereRaw('orders.user_id = users.id');
    })
    ->get();
```

---

## ⚡ Performance & Optimization

### Indexing Strategy
```php
// Create indexes in migrations
Schema::table('orders', function (Blueprint $table) {
    $table->index('user_id');
    $table->index('created_at');
    $table->index(['status', 'created_at']); // Composite index
});
```

### Query Optimization Tips
```php
// Use specific columns instead of *
->select(['id', 'name', 'email'])

// Limit results
->limit(100)

// Use chunk for large datasets
DB::table('users')->chunk(200, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});

// Eager loading for relationships
User::with('posts')->get();
```

### SQL Query Analysis
```php
// Debug queries
DB::listen(function ($query) {
    Log::info($query->sql);
    Log::info($query->bindings);
    Log::info($query->time);
});

// Get SQL and bindings
$query = DB::table('users')->where('active', 1);
dd($query->toSql(), $query->getBindings());
```

---

## 🔍 Database Schema Checks

### Column Existence Check
```php
// Check if column exists (from your code)
DB::raw("COALESCE(
    CASE
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns
            WHERE table_schema = DATABASE()
            AND table_name = 'table_name'
            AND column_name = 'column_name'
        )
        THEN table_name.column_name
        ELSE 0
    END, 0
) as safe_column")
```

### Schema Information Queries
```php
// Get table columns
DB::select("
    SELECT column_name, data_type, column_default
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
    AND table_name = 'users'
");

// Check table exists
DB::select("
    SELECT 1 FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name = 'table_name'
");
```

---

## 🎨 Common Patterns

### CSV Export Pattern
```php
public function exportCSV($data) {
    $csvData[] = ['Column1', 'Column2', 'Column3']; // Headers
    
    foreach ($data as $row) {
        $csvData[] = [
            $row->column1 ?? '',
            $row->column2 ?? '',
            $row->column3 ?? ''
        ];
    }
    
    $csvString = '';
    foreach ($csvData as $row) {
        $csvString .= implode(',', $row) . PHP_EOL;
    }
    
    return $csvString;
}
```

### Null Coalescing Patterns
```php
// PHP null coalescing
$value = $row->column ?? 'default_value';

// SQL COALESCE
DB::raw("COALESCE(column_name, 'default_value') as safe_column")
DB::raw("COALESCE(column1, column2, column3, 0) as cascading_value")
```

### Dynamic Column Selection
```php
private static $standardColumns = [
    'id', 'order_ref_id', 'sold_by', 'provider_name',
    'package_name', 'payment_method', 'commission'
    // ... more columns
];

// Use in queries
->select(self::$standardColumns)

// Map data to standard format
array_map(function ($column) use ($order) {
    return $order->{$column} ?? '';
}, self::$standardColumns);
```

---

## 🛠️ Debugging & Testing

### Query Debugging
```php
// Enable query log
DB::enableQueryLog();

// Run your queries
$users = DB::table('users')->get();

// Get executed queries
$queries = DB::getQueryLog();
dd($queries);
```

### Raw SQL Execution
```php
// Execute raw SQL
DB::select('SELECT * FROM users WHERE id = ?', [1]);
DB::insert('INSERT INTO users (name, email) VALUES (?, ?)', ['John', 'john@example.com']);
DB::update('UPDATE users SET name = ? WHERE id = ?', ['Jane', 1]);
DB::delete('DELETE FROM users WHERE id = ?', [1]);
```

### Transaction Handling
```php
DB::transaction(function () {
    DB::table('users')->update(['votes' => 1]);
    DB::table('posts')->delete();
});

// Manual transaction control
DB::beginTransaction();
try {
    DB::table('users')->update(['votes' => 1]);
    DB::table('posts')->delete();
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    throw $e;
}
```

### Solve N+1 problem, Eager Loading with with()

```php

$users = User::with('posts')->get();

foreach ($users as $user) {
    echo $user->posts->count();
}
```

### Nested Egerloading

```php

$users = User::with('posts.comments')->get();
```

### Conditional Eger Loading : 

```php
$users = User::with(['posts' => function ($query) {
    $query->where('published', true);
}])->get();
```

### 🛠 Tip: Use load() for Already Fetched Models
```php
$users = User::all();
$users->load('posts');

```

### Django vs Laravel Eger Loading : 

| Django                               | Laravel                                          | Purpose                                                               |
| ------------------------------------ | ------------------------------------------------ | --------------------------------------------------------------------- |
| `select_related('relation')`         | `with('relation')`                               | Eager load a **single related object** (ForeignKey or OneToOne)       |
| `prefetch_related('relation')`       | `with('relation')`                               | Eager load a **"many" relationship** (ManyToMany, reverse ForeignKey) |
| `Prefetch('relation', queryset=...)` | `with(['relation' => fn($q) => $q->where(...)])` | Add conditions to the prefetch                                        |
| `only('field1', 'field2')`           | `select('field1', 'field2')`                     | Select specific columns only                                          |
| `defer('field1')`                    | No direct equivalent                             | Avoid loading specific fields                                         |
| `prefetch_related_objects()`         | Not needed                                       | Laravel handles collection prefetching via `with()`                   |



---

## 💡 Pro Tips

1. **Always use parameter binding** to prevent SQL injection
2. **Use COALESCE** for null handling in SQL
3. **Cast data types** when dealing with different character sets
4. **Check column existence** before using dynamic columns
5. **Use unions** for combining similar datasets
6. **Index frequently queried columns**
7. **Chunk large datasets** to prevent memory issues
8. **Use raw expressions** for complex calculations
9. **Test queries with EXPLAIN** for performance analysis
10. **Use database transactions** for data integrity

---

## 🎯 Quick Reference Commands

```bash
# Laravel Artisan Commands
php artisan make:migration create_table_name
php artisan migrate
php artisan tinker

# MySQL Commands
SHOW TABLES;
DESCRIBE table_name;
EXPLAIN SELECT * FROM table_name WHERE condition;
SHOW INDEX FROM table_name;
```

---

*Master these patterns and you'll be ready to handle any Laravel ORM or SQL challenge! 🚀*
