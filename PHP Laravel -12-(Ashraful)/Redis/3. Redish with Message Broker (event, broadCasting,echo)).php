<?php

// 1. First, ensure Redis is installed and configured in .env
// REDIS_HOST=127.0.0.1
// REDIS_PASSWORD=null
// REDIS_PORT=6379

// 2. Create Order Model
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'total', 'status'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// 3. Create OrderPlacedEvent
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('orders');
    }

    public function broadcastAs()
    {
        return 'order.placed';
    }
}

// 4. Create OrderProcessor Job
namespace App\Jobs;

use App\Models\Order;
use App\Events\OrderPlacedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class OrderProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        // Process order logic here
        try {
            // Simulate order processing
            sleep(2);
            
            // Update order status
            $this->order->status = 'processing';
            $this->order->save();

            // Publish message to Redis channel
            event(new OrderPlacedEvent($this->order));

            // Add to Redis list for tracking
            Redis::rpush('processed_orders', json_encode([
                'order_id' => $this->order->id,
                'processed_at' => now()->toDateTimeString(),
                'status' => 'processing'
            ]));

        } catch (\Exception $e) {
            // Log error and add to failed orders list
            Redis::rpush('failed_orders', json_encode([
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'failed_at' => now()->toDateTimeString()
            ]));
            
            throw $e;
        }
    }
}

// 5. Create OrderController
namespace App\Http\Controllers;

use App\Models\Order;
use App\Jobs\OrderProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total' => 'required|numeric',
        ]);

        $order = Order::create([
            'user_id' => $validatedData['user_id'],
            'total' => $validatedData['total'],
            'status' => 'pending'
        ]);

        // Dispatch order processing job
        OrderProcessor::dispatch($order);

        return response()->json([
            'message' => 'Order placed successfully',
            'order_id' => $order->id
        ]);
    }

    public function getProcessedOrders()
    {
        $processedOrders = Redis::lrange('processed_orders', 0, -1);
        return response()->json(array_map('json_decode', $processedOrders));
    }

    public function getFailedOrders()
    {
        $failedOrders = Redis::lrange('failed_orders', 0, -1);
        return response()->json(array_map('json_decode', $failedOrders));
    }
}

// 6. Create WebSocket Consumer (in resources/js/components/OrderNotifications.vue)
<template>
  <div class="order-notifications">
    <h3>Real-time Order Updates</h3>
    <div v-for="notification in notifications" :key="notification.id">
      <div class="notification">
        Order #{{ notification.order.id }} - {{ notification.message }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      notifications: []
    }
  },
  
  mounted() {
    // Listen to Redis channel through Laravel Echo
    Echo.channel('orders')
      .listen('.order.placed', (event) => {
        this.notifications.unshift({
          id: Date.now(),
          order: event.order,
          message: `New order processed for $${event.order.total}`
        });
      });
  }
}
</script>

// 7. Configure Laravel Echo in resources/js/bootstrap.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// 8. Add routes in routes/api.php
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/processed', [OrderController::class, 'getProcessedOrders']);
Route::get('/orders/failed', [OrderController::class, 'getFailedOrders']);

// 9. Run queue worker
// php artisan queue:work redis

// Usage Example:

// Place new order
$response = Http::post('/api/orders', [
    'user_id' => 1,
    'total' => 299.99
]);

// Get processed orders
$processedOrders = Http::get('/api/orders/processed');

// Get failed orders
$failedOrders = Http::get('/api/orders/failed');
