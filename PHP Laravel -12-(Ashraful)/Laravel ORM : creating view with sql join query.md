# Laravel ORM with MySQL Views - Tutorial

## 🎯 What are MySQL Views?
A **MySQL View** is a virtual table based on SQL queries. It doesn't store data but shows data from other tables in a specific format.

**Benefits:**
- ✅ Simplify complex queries
- ✅ Combine data from multiple tables
- ✅ Reuse common queries
- ✅ Better performance for complex JOINs

---

## 🔧 Creating Views with Laravel ORM

### Method 1: Direct SQL in Laravel
```php
// Create a simple view
DB::statement("
    CREATE VIEW customer_orders AS
    SELECT id, name, total_amount, created_at
    FROM orders
    WHERE status = 'completed'
");
```

### Method 2: Laravel Query Builder + View
```php
// Build query with Laravel ORM
$query = DB::table('orders as o')
    ->leftJoin('customers as c', 'o.customer_id', '=', 'c.id')
    ->leftJoin('products as p', 'o.product_id', '=', 'p.id')
    ->select([
        'o.id as order_id',
        'c.name as customer_name',
        'p.name as product_name',
        'o.total_amount',
        'o.created_at'
    ])
    ->where('o.status', 'completed');

// Create view from Laravel query
DB::statement("CREATE VIEW order_summary AS ({$query->toSql()})", $query->getBindings());
```

---

## 🔄 UNION Queries with Views

### Combining Multiple Tables
```php
// Query 1: Online Orders
$onlineOrders = DB::table('online_orders')
    ->select([
        'id as order_id',
        'customer_name',
        'amount',
        DB::raw("'Online' as order_type"),
        'created_at'
    ]);

// Query 2: Store Orders  
$storeOrders = DB::table('store_orders')
    ->select([
        'id as order_id', 
        'customer_name',
        'amount',
        DB::raw("'Store' as order_type"),
        'created_at'
    ]);

// Combine with UNION
$unionQuery = $onlineOrders->union($storeOrders);

// Create view
DB::statement("CREATE VIEW all_orders AS ({$unionQuery->toSql()})", $unionQuery->getBindings());
```

---

## 🛡️ Error Handling & Best Practices

### Safe Column Handling
```php
// Use COALESCE for missing columns
DB::raw('COALESCE(column_name, "default_value") as safe_column')

// Handle NULL values
DB::raw('COALESCE(price, 0) as price')
DB::raw('COALESCE(status, "pending") as status')
```

### Check if View Exists
```php
private function createViewIfNotExists()
{
    try {
        // Check if view exists
        $viewExists = DB::select("SHOW TABLES LIKE 'my_view'");
        
        if (empty($viewExists)) {
            // Drop if exists (for recreation)
            DB::statement("DROP VIEW IF EXISTS my_view");
            
            // Create new view
            $this->createMyView();
        }
    } catch (\Exception $e) {
        Log::error('View creation failed: ' . $e->getMessage());
    }
}
```

---

## 📊 Real Example: Customer Dashboard

```php
class DashboardController extends Controller 
{
    public function dashboard()
    {
        // Create views on first load
        $this->createDashboardViews();
        
        // Use views for data
        $totals = $this->getTotals($userId);
        
        return view('dashboard', compact('totals'));
    }
    
    private function createDashboardViews()
    {
        // Orders from multiple categories
        $lifeInsurance = DB::table('life_orders as lo')
            ->leftJoin('customers as c', 'lo.customer_id', '=', 'c.id')
            ->select([
                'lo.id as order_id',
                'c.name as customer_name', 
                'lo.amount',
                DB::raw("'Life Insurance' as category"),
                'lo.created_at'
            ]);
            
        $motorInsurance = DB::table('motor_orders as mo')
            ->leftJoin('customers as c', 'mo.customer_id', '=', 'c.id')
            ->select([
                'mo.id as order_id',
                'c.name as customer_name',
                'mo.amount', 
                DB::raw("'Motor Insurance' as category"),
                'mo.created_at'
            ]);
            
        // Combine all categories
        $allOrders = $lifeInsurance->union($motorInsurance);
        
        // Create view
        DB::statement("CREATE VIEW customer_all_orders AS ({$allOrders->toSql()})", $allOrders->getBindings());
    }
    
    private function getTotals($userId)
    {
        return DB::select("
            SELECT 
                COUNT(*) as total_orders,
                SUM(amount) as total_spent
            FROM customer_all_orders 
            WHERE customer_id = ?
        ", [$userId]);
    }
}
```

---

## 🎓 Student Exercise

**Task:** Create a view that shows:
1. All student enrollments from multiple courses
2. Combine `web_development_enrollments` and `mobile_app_enrollments` tables
3. Show: student_name, course_name, enrollment_date, course_type
4. Use Laravel ORM with UNION

**Solution Structure:**
```php
// Step 1: Build individual queries
$webCourses = DB::table('web_development_enrollments')...
$mobileCourses = DB::table('mobile_app_enrollments')...

// Step 2: Union them
$allEnrollments = $webCourses->union($mobileCourses);

// Step 3: Create view
DB::statement("CREATE VIEW student_enrollments AS ({$allEnrollments->toSql()})", $allEnrollments->getBindings());
```

---

## 🔑 Key Takeaways

1. **Views = Virtual Tables** - No data storage, just query results
2. **Laravel ORM + Views** - Build with Query Builder, create with `DB::statement()`
3. **UNION** - Combine similar data from different tables
4. **Error Handling** - Always use `COALESCE()` and `try-catch`
5. **Performance** - Views can improve complex query performance
6. **Reusability** - Create once, use multiple times

**Remember:** Views are perfect for dashboards, reports, and combining data from multiple sources! 🚀
