# Email Service Integration Guide

This guide covers setting up email services for Graphic School 2.0 production.

---

## Supported Providers

1. **SendGrid** (Recommended)
2. **Mailgun** (Alternative)
3. **SMTP** (Fallback)

---

## Option A: SendGrid Setup

### 1. Create SendGrid Account

1. Sign up at https://sendgrid.com
2. Verify your account
3. Create an API key

### 2. Configure Domain

1. Go to **Settings > Sender Authentication**
2. Click **Authenticate Your Domain**
3. Add DNS records:
   - **CNAME Records** (for domain verification)
   - **SPF Record**: `v=spf1 include:sendgrid.net ~all`
   - **DKIM Records** (provided by SendGrid)
   - **DMARC Record**: `v=DMARC1; p=none; rua=mailto:dmarc@yourdomain.com`

### 3. Environment Configuration

Add to `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Graphic School"
```

### 4. Test Email

```bash
php artisan tinker
>>> Mail::raw('Test email', function($message) { $message->to('test@example.com')->subject('Test'); });
```

---

## Option B: Mailgun Setup

### 1. Create Mailgun Account

1. Sign up at https://mailgun.com
2. Verify your account
3. Add and verify domain

### 2. Configure Domain

1. Go to **Sending > Domains**
2. Add your domain
3. Add DNS records:
   - **TXT Record** (for domain verification)
   - **SPF Record**: `v=spf1 include:mailgun.org ~all`
   - **DKIM Records** (provided by Mailgun)
   - **DMARC Record**: `v=DMARC1; p=none; rua=mailto:dmarc@yourdomain.com`

### 3. Environment Configuration

Add to `.env`:

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=yourdomain.com
MAILGUN_SECRET=your_mailgun_secret_key
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Graphic School"
```

### 4. Install Mailgun Package

```bash
composer require symfony/mailgun-mailer symfony/http-client
```

---

## Option C: SMTP (Generic)

### 1. Environment Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Graphic School"
```

---

## Email Verification Routes

### Add Routes

Add to `routes/api.php`:

```php
// Email Verification
Route::middleware(['auth:api'])->group(function () {
    Route::post('/email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1');
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])
        ->name('verification.verify');
});
```

### Create Controller

Create `app/Http/Controllers/Auth/EmailVerificationController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email already verified');
        }

        $request->user()->sendEmailVerificationNotification();

        return ApiResponse::success(null, 'Verification email sent');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = \Modules\ACL\Users\Models\User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return ApiResponse::error('Invalid verification link', [], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email already verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return ApiResponse::success(null, 'Email verified successfully');
    }
}
```

---

## DNS Records Summary

### SPF Record
```
v=spf1 include:sendgrid.net ~all
# or
v=spf1 include:mailgun.org ~all
```

### DKIM Record
(Provided by email service provider)

### DMARC Record
```
v=DMARC1; p=none; rua=mailto:dmarc@yourdomain.com; ruf=mailto:dmarc@yourdomain.com; fo=1
```

---

## Testing Email

### Test Command

Create `app/Console/Commands/TestEmail.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Send a test email';

    public function handle()
    {
        $email = $this->argument('email');
        
        Mail::raw('This is a test email from Graphic School', function($message) use ($email) {
            $message->to($email)
                    ->subject('Test Email - Graphic School');
        });

        $this->info("Test email sent to {$email}");
    }
}
```

### Usage

```bash
php artisan email:test your@email.com
```

---

## Email Templates

### Create Mailable

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Welcome to Graphic School')
                    ->view('emails.welcome')
                    ->with(['user' => $this->user]);
    }
}
```

### Queue Emails

```php
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

Mail::to($user->email)
    ->queue(new WelcomeEmail($user));
```

---

## Monitoring

### Check Email Delivery

1. **SendGrid**: Dashboard > Activity
2. **Mailgun**: Logs > Events
3. **SMTP**: Check mail server logs

### Bounce Handling

Configure bounce webhooks:

1. **SendGrid**: Settings > Mail Settings > Event Webhook
2. **Mailgun**: Webhooks > Bounce

---

## Best Practices

1. **Use Queues**: Always queue emails to avoid blocking requests
2. **Retry Logic**: Configure retry attempts for failed emails
3. **Rate Limiting**: Respect email provider rate limits
4. **Unsubscribe**: Include unsubscribe links in marketing emails
5. **Monitoring**: Monitor bounce rates and delivery rates

---

**Email service setup complete!** 🎉

