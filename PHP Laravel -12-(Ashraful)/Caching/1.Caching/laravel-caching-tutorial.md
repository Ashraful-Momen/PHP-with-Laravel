# Laravel Caching with CRUD Tutorial

## Prerequisites
- Laravel 10.x
- Redis (recommended) or any cache driver
- Basic understanding of Laravel

## 1. Project Setup

First, set up your cache configuration in `.env`:

```env
CACHE_DRIVER=redis  # or file, memcached
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 2. Create a Blog Post Model and Migration

```bash
php artisan make:model Post -m
```

Update migration (`database/migrations/xxxx_create_posts_table.php`):
```php
public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->string('author');
        $table->timestamps();
    });
}
```

## 3. Create Post Controller with Caching

```bash
php artisan make:controller PostController
```

Here's a complete `PostController` with caching implementation:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    private $cacheTimeout = 3600; // 1 hour

    /**
     * Generate cache key for a specific post
     */
    private function getPostCacheKey($postId)
    {
        return "post.{$postId}";
    }

    /**
     * Generate cache key for all posts
     */
    private function getAllPostsCacheKey()
    {
        return "posts.all";
    }

    /**
     * Clear all related caches
     */
    private function clearPostCaches($postId)
    {
        Cache::forget($this->getPostCacheKey($postId));
        Cache::forget($this->getAllPostsCacheKey());
    }

    /**
     * Display a listing of posts
     */
    public function index()
    {
        return Cache::remember($this->getAllPostsCacheKey(), $this->cacheTimeout, function () {
            return Post::latest()->paginate(10);
        });
    }

    /**
     * Store a new post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'author' => 'required|max:100'
        ]);

        $post = Post::create($validated);
        
        // Clear the all posts cache as we have a new post
        Cache::forget($this->getAllPostsCacheKey());

        return response()->json($post, 201);
    }

    /**
     * Display a specific post
     */
    public function show($id)
    {
        return Cache::remember($this->getPostCacheKey($id), $this->cacheTimeout, function () use ($id) {
            return Post::findOrFail($id);
        });
    }

    /**
     * Update a post
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|max:255',
            'content' => 'sometimes|required',
            'author' => 'sometimes|required|max:100'
        ]);

        $post = Post::findOrFail($id);
        $post->update($validated);

        // Clear both specific post cache and all posts cache
        $this->clearPostCaches($id);

        return response()->json($post);
    }

    /**
     * Delete a post
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        // Clear both specific post cache and all posts cache
        $this->clearPostCaches($id);

        return response()->json(null, 204);
    }

    /**
     * Get posts by author with caching
     */
    public function getByAuthor($author)
    {
        $cacheKey = "posts.author.{$author}";

        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($author) {
            return Post::where('author', $author)->get();
        });
    }
}
```

## 4. Create Routes

Update `routes/api.php`:
```php
use App\Http\Controllers\PostController;

Route::apiResource('posts', PostController::class);
Route::get('posts/author/{author}', [PostController::class, 'getByAuthor']);
```

## 5. Advanced Caching Techniques

### 5.1 Cache Tags (Redis or Memcached only)

If you're using Redis or Memcached, you can use cache tags for better organization:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    private $cacheTimeout = 3600;

    public function index()
    {
        return Cache::tags(['posts'])->remember('all', $this->cacheTimeout, function () {
            return Post::latest()->paginate(10);
        });
    }

    public function show($id)
    {
        return Cache::tags(['posts', "post-{$id}"])->remember('details', $this->cacheTimeout, function () use ($id) {
            return Post::findOrFail($id);
        });
    }

    public function clearCache($id)
    {
        Cache::tags(['posts', "post-{$id}"])->flush();
    }
}
```

### 5.2 Automatic Cache Clearing with Model Events

Create a model observer to automatically handle cache:

```bash
php artisan make:observer PostObserver
```

Update `app/Observers/PostObserver.php`:
```php
<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function saved(Post $post)
    {
        $this->clearCache($post);
    }

    public function deleted(Post $post)
    {
        $this->clearCache($post);
    }

    private function clearCache(Post $post)
    {
        Cache::forget("post.{$post->id}");
        Cache::forget("posts.all");
        Cache::forget("posts.author.{$post->author}");
        
        // If using cache tags
        if (config('cache.default') === 'redis' || config('cache.default') === 'memcached') {
            Cache::tags(['posts', "post-{$post->id}"])->flush();
        }
    }
}
```

Register the observer in `app/Providers/EventServiceProvider.php`:
```php
use App\Models\Post;
use App\Observers\PostObserver;

public function boot()
{
    Post::observe(PostObserver::class);
}
```

## 6. Usage Examples

### 6.1 Create a Post
```bash
curl -X POST http://your-app.test/api/posts \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Post",
    "content": "This is the content",
    "author": "John Doe"
  }'
```

### 6.2 Get All Posts
```bash
curl http://your-app.test/api/posts
```

### 6.3 Get Single Post
```bash
curl http://your-app.test/api/posts/1
```

### 6.4 Update Post
```bash
curl -X PUT http://your-app.test/api/posts/1 \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Title"
  }'
```

### 6.5 Delete Post
```bash
curl -X DELETE http://your-app.test/api/posts/1
```

## 7. Cache Management Commands

Clear all cache:
```bash
php artisan cache:clear
```

Clear specific cache key:
```php
Cache::forget('key');
```

Clear cache tags:
```php
Cache::tags(['posts'])->flush();
```

## Best Practices

1. **Cache Keys**: Always use consistent and descriptive cache keys
2. **Cache Duration**: Set appropriate cache timeout based on data update frequency
3. **Cache Clearing**: Clear related caches when data is modified
4. **Error Handling**: Always handle cache failures gracefully
5. **Cache Tags**: Use cache tags when available for better organization
6. **Cache Race Conditions**: Use atomic operations or locks for critical data

## Performance Tips

1. Use Redis for better performance
2. Set appropriate cache timeouts
3. Implement cache warming for frequently accessed data
4. Monitor cache hit/miss rates
5. Use cache tags for efficient cache management
6. Implement cache versioning for complex data structures
