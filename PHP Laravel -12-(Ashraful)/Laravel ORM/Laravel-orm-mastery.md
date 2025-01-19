# Laravel ORM Mastery Guide

## Table of Contents
1. [Basic Model Setup](#basic-model-setup)
2. [One to One Relationships](#one-to-one-relationships)
3. [One to Many Relationships](#one-to-many-relationships)
4. [Many to Many Relationships](#many-to-many-relationships)
5. [Polymorphic Relationships](#polymorphic-relationships)
6. [Model Boot Method and Events](#model-boot-method-and-events)
7. [Advanced Query Techniques](#advanced-query-techniques)
8. [Best Practices and Performance](#best-practices-and-performance)

## Basic Model Setup

### Model Structure and Configuration
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory, SoftDeletes;

    // Specify table if different from convention
    protected $table = 'users';
    
    // Specify primary key if not 'id'
    protected $primaryKey = 'user_id';
    
    // Mass assignable attributes
    protected $fillable = [
        'name', 'email', 'password'
    ];
    
    // Hidden attributes in arrays/JSON
    protected $hidden = [
        'password', 'remember_token'
    ];
    
    // Auto-cast attributes
    protected $casts = [
        'email_verified_at' => 'datetime',
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    // Default values
    protected $attributes = [
        'is_active' => true,
        'settings' => '{"theme": "light"}'
    ];
}
```

## One to One Relationships

### Basic One to One
```php
// User Model
class User extends Model
{
    public function profile()
    {
        return $this->hasOne(Profile::class);
        // Customized: return $this->hasOne(Profile::class, 'user_id', 'id');
    }
}

// Profile Model
class Profile extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
        // Customized: return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
```

### Usage Examples
```php
// Create with relationship
$user = User::create(['name' => 'John Doe']);
$user->profile()->create(['bio' => 'Laravel Developer']);

// Eager loading
$user = User::with('profile')->find(1);

// Lazy loading
$userBio = $user->profile->bio;

// Create with nested relationship
User::create([
    'name' => 'John Doe',
    'profile' => [
        'bio' => 'Laravel Developer'
    ]
]);
```

## One to Many Relationships

### Basic One to Many
```php
// User Model
class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

// Post Model
class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Advanced One to Many Examples
```php
// With constraints
class User extends Model
{
    public function publishedPosts()
    {
        return $this->hasMany(Post::class)
            ->where('status', 'published');
    }
    
    public function latestPosts()
    {
        return $this->hasMany(Post::class)
            ->latest()
            ->take(5);
    }
}

// Usage examples
$user->posts()->create([
    'title' => 'New Post',
    'content' => 'Content here'
]);

// Querying relationship existence
$usersWithPosts = User::has('posts')->get();
$usersWithManyPosts = User::has('posts', '>=', 5)->get();

// Querying relationship absence
$usersWithNoPosts = User::doesntHave('posts')->get();

// Counting related models
$users = User::withCount('posts')->get();
foreach ($users as $user) {
    echo $user->posts_count;
}
```

## Many to Many Relationships

### Basic Many to Many
```php
// User Model
class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps()
            ->withPivot('expires_at');
    }
}

// Role Model
class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot('expires_at');
    }
}
```

### Advanced Many to Many Usage
```php
// Attaching/Detaching relationships
$user->roles()->attach($roleId);
$user->roles()->attach($roleId, ['expires_at' => now()->addDays(30)]);
$user->roles()->detach($roleId);
$user->roles()->sync([1, 2, 3]); // Sync specific roles
$user->roles()->toggle([1, 2, 3]); // Toggle relationships

// Querying pivot data
$user->roles()->wherePivot('expires_at', '>', now())->get();

// Custom intermediate table model
class RoleUser extends Pivot
{
    protected $table = 'role_user';
    
    protected $casts = [
        'expires_at' => 'datetime'
    ];
    
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}

// Using custom pivot model
public function roles()
{
    return $this->belongsToMany(Role::class)
        ->using(RoleUser::class);
}
```

## Polymorphic Relationships

### One to One Polymorphic
```php
// Image Model
class Image extends Model
{
    public function imageable()
    {
        return $this->morphTo();
    }
}

// User Model
class User extends Model
{
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

// Post Model
class Post extends Model
{
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
```

### One to Many Polymorphic
```php
// Comment Model
class Comment extends Model
{
    public function commentable()
    {
        return $this->morphTo();
    }
}

// Post Model
class Post extends Model
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

// Video Model
class Video extends Model
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
```

### Many to Many Polymorphic
```php
// Tag Model
class Tag extends Model
{
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }
    
    public function videos()
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }
}

// Post Model
class Post extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
```

## Model Boot Method and Events

### Basic Boot Method
```php
class User extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        // Always include certain relationships
        static::addGlobalScope('withProfile', function ($query) {
            $query->with('profile');
        });
        
        // Set default attributes
        static::creating(function ($user) {
            $user->uuid = (string) Str::uuid();
        });
    }
}
```

### Advanced Event Handling
```php
class Post extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        // Before Create
        static::creating(function ($post) {
            $post->slug = Str::slug($post->title);
        });
        
        // After Create
        static::created(function ($post) {
            Cache::tags('posts')->flush();
            event(new PostCreated($post));
        });
        
        // Before Update
        static::updating(function ($post) {
            if ($post->isDirty('title')) {
                $post->slug = Str::slug($post->title);
            }
        });
        
        // Before Delete
        static::deleting(function ($post) {
            $post->comments()->delete();
            $post->tags()->detach();
        });
        
        // After Delete
        static::deleted(function ($post) {
            Storage::delete($post->image_path);
        });
        
        // After Restore (with soft deletes)
        static::restored(function ($post) {
            $post->comments()->restore();
        });
    }
}
```

### Real-time Updates with Events
```php
class Post extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($post) {
            broadcast(new PostCreated($post))->toOthers();
        });
        
        static::updated(function ($post) {
            $changes = $post->getChanges();
            broadcast(new PostUpdated($post, $changes))->toOthers();
        });
        
        static::deleted(function ($post) {
            broadcast(new PostDeleted($post->id))->toOthers();
        });
    }
}

// Broadcasting Event
class PostUpdated implements ShouldBroadcast
{
    public $post;
    public $changes;
    
    public function __construct(Post $post, array $changes)
    {
        $this->post = $post;
        $this->changes = $changes;
    }
    
    public function broadcastOn()
    {
        return new PresenceChannel('posts');
    }
}
```

## Advanced Query Techniques

### Query Scopes
```php
class Post extends Model
{
    // Global Scope
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('published', function ($query) {
            $query->where('status', 'published');
        });
    }
    
    // Local Scopes
    public function scopePopular($query)
    {
        return $query->where('views', '>', 1000);
    }
    
    public function scopePublishedBetween($query, $start, $end)
    {
        return $query->whereBetween('published_at', [$start, $end]);
    }
    
    // Dynamic Scope
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}

// Usage
$posts = Post::popular()
    ->publishedBetween($start, $end)
    ->ofType('tutorial')
    ->get();
```

### Advanced Querying
```php
// Complex Where Clauses
$posts = Post::where(function ($query) {
    $query->where('status', 'published')
        ->orWhere(function ($query) {
            $query->where('status', 'draft')
                ->where('user_id', auth()->id());
        });
})->get();

// Sub-queries
$users = User::addSelect(['last_post_title' => 
    Post::select('title')
        ->whereColumn('user_id', 'users.id')
        ->latest()
        ->limit(1)
])->get();

// Conditional Loading
$posts = Post::with(['user' => function ($query) {
    $query->select('id', 'name');
}])->get();

// Nested Eager Loading
$posts = Post::with([
    'comments',
    'comments.user',
    'comments.replies.user'
])->get();
```

## Best Practices and Performance

### Optimization Techniques
```php
// Select specific columns
User::select('id', 'name', 'email')->get();

// Chunk large datasets
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});

// Lazy loading collections
User::cursor()->each(function ($user) {
    // Process user
});

// Caching queries
$users = Cache::remember('users', 3600, function () {
    return User::all();
});
```

### Model Events for Maintaining Integrity
```php
class Post extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        // Update counts
        static::created(function ($post) {
            $post->user->increment('posts_count');
        });
        
        static::deleted(function ($post) {
            $post->user->decrement('posts_count');
        });
        
        // Clean up relationships
        static::deleting(function ($post) {
            $post->comments()->delete();
            $post->tags()->detach();
            Cache::tags(['posts', "post-{$post->id}"])->flush();
        });
    }
}
```

Remember to:
- Use eager loading to avoid N+1 query problems
- Implement caching strategies for frequently accessed data
- Use database transactions for complex operations
- Consider implementing model observers for cleaner code
- Use query scopes for reusable query constraints
- Implement proper validation and authorization
- Monitor query performance using Laravel's built-in tools

The examples provided should be adapted based on your specific needs and requirements.
