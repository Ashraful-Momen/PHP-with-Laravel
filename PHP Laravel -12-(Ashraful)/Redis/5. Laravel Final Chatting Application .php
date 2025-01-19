// config/broadcasting.php
'default' => env('BROADCAST_DRIVER', 'redis'),
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
    ],
],

// app/Events/ChatMessageEvent.php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Message;

class ChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public User $user;

    public function __construct(User $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('chat.room.' . $this->message->room_id);
    }

    public function broadcastAs(): string
    {
        return 'new.message';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name
            ],
            'timestamp' => $this->message->created_at->toIso8601String()
        ];
    }
}

// app/Models/Message.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['content', 'room_id', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}

// app/Models/Room.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = ['name'];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}

// app/Services/ChatService.php
<?php

namespace App\Services;

use App\Events\ChatMessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;

class ChatService
{
    private BroadcastFactory $broadcaster;

    public function __construct(BroadcastFactory $broadcaster)
    {
        $this->broadcaster = $broadcaster;
    }

    public function sendMessage(User $user, string $content, int $roomId): Message
    {
        $message = new Message([
            'content' => $content,
            'room_id' => $roomId,
            'user_id' => $user->id
        ]);
        
        $message->save();

        // Explicitly broadcast the event
        $event = new ChatMessageEvent($user, $message);
        $this->broadcaster->event($event);

        return $message;
    }
}

// app/Http/Controllers/ChatController.php
<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'room_id' => 'required|integer|exists:rooms,id'
        ]);

        $message = $this->chatService->sendMessage(
            $request->user(),
            $validated['content'],
            $validated['room_id']
        );

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

    public function getMessages(Request $request, int $roomId): JsonResponse
    {
        $messages = Message::with('user')
            ->where('room_id', $roomId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($messages);
    }
}

// resources/js/bootstrap.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

// resources/views/chat.blade.php
<!DOCTYPE html>
<html>
<head>
    <title>Chat Room</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="chat-app" class="container mx-auto p-4">
        <div class="messages-container bg-white shadow-lg rounded-lg p-4 mb-4" style="height: 500px; overflow-y: auto;">
            <div id="messages"></div>
        </div>
        
        <div class="input-container">
            <form id="message-form" class="flex gap-2">
                <input type="hidden" id="room-id" value="{{ $roomId }}">
                <input type="text" 
                       id="message-input" 
                       class="flex-1 border rounded-lg px-4 py-2" 
                       placeholder="Type your message...">
                <button type="submit" 
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg">
                    Send
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messages');
            const messageForm = document.getElementById('message-form');
            const messageInput = document.getElementById('message-input');
            const roomId = document.getElementById('room-id').value;

            // Subscribe to the chat room channel
            window.Echo.channel(`chat.room.${roomId}`)
                .listen('.new.message', (event) => {
                    appendMessage(event);
                });

            // Load existing messages
            fetch(`/api/chat/rooms/${roomId}/messages`)
                .then(response => response.json())
                .then(messages => {
                    messages.reverse().forEach(message => {
                        appendMessage({
                            id: message.id,
                            content: message.content,
                            user: message.user,
                            timestamp: message.created_at
                        });
                    });
                });

            // Handle message submission
            messageForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const content = messageInput.value.trim();
                if (!content) return;

                try {
                    const response = await fetch('/api/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            content: content,
                            room_id: roomId
                        })
                    });

                    if (!response.ok) throw new Error('Failed to send message');
                    
                    messageInput.value = '';
                } catch (error) {
                    console.error('Error sending message:', error);
                    alert('Failed to send message. Please try again.');
                }
            });

            function appendMessage(message) {
                const messageElement = document.createElement('div');
                messageElement.className = 'message mb-4';
                messageElement.innerHTML = `
                    <div class="flex items-start gap-2">
                        <div class="font-bold">${message.user.name}:</div>
                        <div class="flex-1">${message.content}</div>
                        <div class="text-sm text-gray-500">
                            ${new Date(message.timestamp).toLocaleTimeString()}
                        </div>
                    </div>
                `;
                messagesContainer.appendChild(messageElement);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });
    </script>
</body>
</html>

// database/migrations/2024_01_19_create_rooms_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

// database/migrations/2024_01_19_create_messages_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

// routes/api.php
<?php

use App\Http\Controllers\ChatController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/rooms/{roomId}/messages', [ChatController::class, 'getMessages']);
});
=========================================================================== Instruction =============================================================================
  I'll create a more explicit implementation of a chat application using Laravel's broadcasting system without helper functions, showing the underlying architecture more clearly.



Key differences in this implementation compared to using helper functions:

1. **Explicit Broadcasting**:
Instead of using the `broadcast()` helper, we:
- Inject the `BroadcastFactory` into our `ChatService`
- Manually create and broadcast events using `$broadcaster->event($event)`

2. **Event Structure**:
- Added explicit return types
- Implemented `broadcastAs()` for custom event names
- Added `broadcastWith()` to customize the broadcasted data

3. **Service Layer**:
- Created a dedicated `ChatService` class to handle message logic
- Explicitly handles event creation and broadcasting
- Better separation of concerns

4. **Database Structure**:
- Added proper migrations for rooms and messages
- Implemented relationships between models
- Added proper type hints and return types

To set this up:

1. Install required packages:
```bash
composer require predis/predis laravel/websockets
```

2. Run migrations:
```bash
php artisan migrate
```

3. Configure your `.env`:
```
BROADCAST_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1
```

4. Start the WebSocket server:
```bash
php artisan websockets:serve
```

This implementation provides:
- Explicit control over broadcasting
- Better type safety
- Clear separation of concerns
- Proper database structure
- Room-based chat support
- Real-time message updates
- Message history loading

Would you like me to explain any specific part in more detail?
