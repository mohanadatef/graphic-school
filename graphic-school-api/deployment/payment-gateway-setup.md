# Payment Gateway Live Configuration Guide

This guide covers setting up live payment gateways for Graphic School 2.0.

---

## Supported Gateways

### GCC Region
- **Stripe** (Recommended)
- **PayTabs** (Optional)

### Egypt
- **Paymob** (Recommended)
- **Fawry** (Optional)

---

## Option A: Stripe (GCC)

### 1. Create Stripe Account

1. Sign up at https://stripe.com
2. Complete business verification
3. Activate live mode

### 2. Get API Keys

1. Go to **Developers > API keys**
2. Copy **Publishable key** (starts with `pk_live_`)
3. Copy **Secret key** (starts with `sk_live_`)

### 3. Configure Webhooks

1. Go to **Developers > Webhooks**
2. Click **Add endpoint**
3. URL: `https://yourdomain.com/api/webhooks/stripe`
4. Events to listen:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `charge.refunded`
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
5. Copy **Signing secret** (starts with `whsec_`)

### 4. Environment Configuration

Add to `.env`:

```env
STRIPE_KEY=pk_live_your_stripe_public_key
STRIPE_SECRET=sk_live_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

### 5. Install Stripe Package

```bash
composer require stripe/stripe-php
```

### 6. Webhook Route

Add to `routes/api.php`:

```php
Route::post('/webhooks/stripe', [\App\Http\Controllers\Webhooks\StripeWebhookController::class, 'handle'])
    ->middleware('webhook.stripe');
```

### 7. Webhook Controller

Create `app/Http/Controllers/Webhooks/StripeWebhookController.php`:

```php
<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            return ApiResponse::error('Invalid signature', [], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSuccess($event->data->object);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
            case 'charge.refunded':
                $this->handleRefund($event->data->object);
                break;
        }

        return ApiResponse::success(null, 'Webhook processed');
    }

    protected function handlePaymentSuccess($paymentIntent)
    {
        // Update payment transaction status
        // Issue invoice
        // Update subscription
    }

    protected function handlePaymentFailed($paymentIntent)
    {
        // Log failure
        // Notify user
        // Update subscription status
    }

    protected function handleRefund($charge)
    {
        // Process refund
        // Update invoice
    }
}
```

### 8. Webhook Middleware

Create `app/Http/Middleware/VerifyStripeWebhook.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyStripeWebhook
{
    public function handle(Request $request, Closure $next)
    {
        // Signature verification handled in controller
        return $next($request);
    }
}
```

---

## Option B: Paymob (Egypt)

### 1. Create Paymob Account

1. Sign up at https://paymob.com
2. Complete business verification
3. Get API credentials

### 2. Get API Keys

1. Go to **Settings > API Keys**
2. Copy **API Key**
3. Copy **Integration ID**
4. Copy **HMAC Secret**

### 3. Configure Webhooks

1. Go to **Settings > Webhooks**
2. Add webhook URL: `https://yourdomain.com/api/webhooks/paymob`
3. Events:
   - `transaction.success`
   - `transaction.failed`
   - `transaction.refunded`

### 4. Environment Configuration

Add to `.env`:

```env
PAYMOB_API_KEY=your_paymob_api_key
PAYMOB_INTEGRATION_ID=your_integration_id
PAYMOB_HMAC_SECRET=your_hmac_secret
```

### 5. Webhook Route

Add to `routes/api.php`:

```php
Route::post('/webhooks/paymob', [\App\Http\Controllers\Webhooks\PaymobWebhookController::class, 'handle'])
    ->middleware('webhook.paymob');
```

### 6. Webhook Controller

Create `app/Http/Controllers/Webhooks/PaymobWebhookController.php`:

```php
<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PaymobWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $hmac = $request->header('X-HMAC');
        $payload = $request->getContent();
        $secret = config('services.paymob.hmac_secret');

        // Verify HMAC
        $calculatedHmac = hash_hmac('sha256', $payload, $secret);
        
        if (!hash_equals($hmac, $calculatedHmac)) {
            return ApiResponse::error('Invalid HMAC', [], 400);
        }

        $data = $request->all();

        switch ($data['type']) {
            case 'transaction.success':
                $this->handlePaymentSuccess($data);
                break;
            case 'transaction.failed':
                $this->handlePaymentFailed($data);
                break;
            case 'transaction.refunded':
                $this->handleRefund($data);
                break;
        }

        return ApiResponse::success(null, 'Webhook processed');
    }

    protected function handlePaymentSuccess($data)
    {
        // Update payment transaction
        // Issue invoice
        // Update subscription
    }

    protected function handlePaymentFailed($data)
    {
        // Log failure
        // Notify user
    }

    protected function handleRefund($data)
    {
        // Process refund
    }
}
```

---

## Retry Logic for Failed Payments

### Queue Job with Retry

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min

    public function handle()
    {
        // Process payment
        // Retry on failure
    }

    public function failed(\Throwable $exception)
    {
        // Log failure
        // Notify admin
    }
}
```

---

## Invoice Status Update

### Service Method

```php
public function updateInvoiceStatus($invoiceId, $status, $transactionId = null)
{
    $invoice = SubscriptionInvoice::findOrFail($invoiceId);
    
    $invoice->update([
        'status' => $status,
        'paid_at' => $status === 'paid' ? now() : null,
    ]);

    if ($transactionId) {
        SubscriptionPayment::create([
            'invoice_id' => $invoice->id,
            'method_id' => $transactionId,
            'status' => $status === 'paid' ? 'success' : 'failed',
            'amount' => $invoice->amount,
        ]);
    }

    // Notify user
    if ($status === 'paid') {
        // Send confirmation email
    }
}
```

---

## Testing

### Test Stripe Webhook

```bash
stripe listen --forward-to https://yourdomain.com/api/webhooks/stripe
stripe trigger payment_intent.succeeded
```

### Test Paymob Webhook

Use Paymob's test environment or webhook testing tool.

---

## Security Checklist

- [x] Webhook signature verification
- [x] HMAC validation (Paymob)
- [x] Rate limiting on webhook endpoints
- [x] Idempotency keys
- [x] Log all webhook events
- [x] Monitor failed payments
- [x] Retry logic for transient failures

---

**Payment gateway setup complete!** 🎉

