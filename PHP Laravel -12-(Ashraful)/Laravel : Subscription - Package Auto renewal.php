Overview: 
------------
implement an auto-subscription system : 

I've created a comprehensive implementation for adding auto-subscription functionality to your insurance packages. Here's how to implement and use this system:

1. First, run the migration to create the subscriptions table. This will store all subscription-related information.

2. The Subscription model handles relationships with users and packages, and includes necessary fields for tracking subscription periods and status.

3. The SubscriptionService class contains the core business logic:
   - createSubscription(): Creates new subscriptions with initial payment
   - renewSubscription(): Handles automatic renewal of subscriptions

4. A scheduled command (ProcessSubscriptionRenewals) runs daily to check for and process upcoming renewals.

5. The SubscriptionController provides endpoints for:
   - Creating new subscriptions
   - Toggling auto-renew status

To integrate this with your existing package system:

1. Add the subscription option to your package purchase flow:
```php
// In your package purchase form
<div class="form-group">
    <label>
        <input type="checkbox" name="auto_renew" value="1">
        Enable automatic renewal
    </label>
</div>
```

2. Update your package model to support subscriptions:
```php
class Package extends Model
{
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
```

3. Set up the scheduled task in your server:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Would you like me to explain any part of this implementation in more detail or help you with the integration into your existing system?

================================================== Coding Part ===============================================

<?php

// database/migrations/xxxx_xx_xx_create_subscriptions_table.php
class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('package_id')->constrained();
            $table->string('status');  // active, cancelled, expired
            $table->timestamp('current_period_start');
            $table->timestamp('current_period_end');
            $table->timestamp('canceled_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamps();
        });
    }
}

// app/Models/Subscription.php
class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'status',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'auto_renew'
    ];

    protected $dates = [
        'current_period_start',
        'current_period_end',
        'canceled_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}

// app/Services/SubscriptionService.php
class SubscriptionService
{
    public function createSubscription(User $user, Package $package, $paymentMethod)
    {
        DB::beginTransaction();
        try {
            // Create initial order
            $order = Order::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'amount' => $package->price,
                'status' => 'pending'
            ]);

            // Process payment
            $payment = $this->processPayment($order, $paymentMethod);
            
            if ($payment->status === 'success') {
                // Create subscription
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'status' => 'active',
                    'current_period_start' => now(),
                    'current_period_end' => now()->addDays($package->duration),
                    'auto_renew' => true
                ]);

                $order->update(['status' => 'completed']);
                DB::commit();
                return $subscription;
            }

            throw new PaymentFailedException('Payment failed');

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function renewSubscription(Subscription $subscription)
    {
        if (!$subscription->auto_renew || $subscription->status !== 'active') {
            return false;
        }

        DB::beginTransaction();
        try {
            $package = $subscription->package;
            
            // Create renewal order
            $order = Order::create([
                'user_id' => $subscription->user_id,
                'package_id' => $package->id,
                'amount' => $package->price,
                'status' => 'pending'
            ]);

            // Process automatic payment
            $payment = $this->processAutomaticPayment($order, $subscription->user);
            
            if ($payment->status === 'success') {
                // Update subscription period
                $subscription->update([
                    'current_period_start' => $subscription->current_period_end,
                    'current_period_end' => $subscription->current_period_end->addDays($package->duration)
                ]);

                $order->update(['status' => 'completed']);
                DB::commit();
                return true;
            }

            throw new PaymentFailedException('Renewal payment failed');

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}

// app/Console/Commands/ProcessSubscriptionRenewals.php
class ProcessSubscriptionRenewals extends Command
{
    protected $signature = 'subscriptions:process-renewals';

    public function handle(SubscriptionService $subscriptionService)
    {
        $subscriptions = Subscription::query()
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->where('current_period_end', '<=', now()->addDays(1))
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $subscriptionService->renewSubscription($subscription);
            } catch (Exception $e) {
                Log::error('Subscription renewal failed', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}

// app/Console/Kernel.php
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('subscriptions:process-renewals')
                ->daily();
    }
}

// app/Http/Controllers/SubscriptionController.php
class SubscriptionController extends Controller
{
    public function store(Request $request, SubscriptionService $subscriptionService)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'payment_method' => 'required'
        ]);

        try {
            $package = Package::findOrFail($validated['package_id']);
            $subscription = $subscriptionService->createSubscription(
                auth()->user(),
                $package,
                $validated['payment_method']
            );

            return response()->json([
                'message' => 'Subscription created successfully',
                'subscription' => $subscription
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Subscription creation failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function toggleAutoRenew(Subscription $subscription)
    {
        $subscription->update([
            'auto_renew' => !$subscription->auto_renew
        ]);

        return response()->json([
            'message' => 'Auto-renew status updated',
            'auto_renew' => $subscription->auto_renew
        ]);
    }
}
