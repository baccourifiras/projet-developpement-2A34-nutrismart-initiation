# Configuration Setup

## API Keys Configuration

This project requires API keys for Stripe and Groq. For security reasons, the actual keys are not committed to Git.

### Step 1: Stripe Configuration

1. Open `Util/StripeService.php`
2. Replace the placeholder keys with your actual Stripe keys:

```php
private $secretKey = 'your_stripe_secret_key_here';
private $publicKey = 'your_stripe_public_key_here';
```

**Get your Stripe keys from:** https://dashboard.stripe.com/test/apikeys

### Step 2: Groq API Configuration

1. Open `config/groq-config.js`
2. Replace the placeholder with your actual Groq API key:

```javascript
apiKey: 'your_groq_api_key_here',
```

**Get your Groq API key from:** https://console.groq.com/keys

### Step 3: Gmail SMTP Configuration

1. Open `Util/EmailService.php`
2. Update the Gmail credentials with your own:
   - Email: your_email@gmail.com
   - App Password: your_app_password_here

**Get Gmail App Password:** https://myaccount.google.com/apppasswords

## Quick Setup Script

Or use the local config files (already have your keys):

```bash
# Copy Groq config
copy config\groq-config.local.js config\groq-config.js

# For Stripe, manually update Util/StripeService.php with your keys
```

## Security Notes

- **Never commit files with actual API keys**
- The `.gitignore` file excludes `*.local.js` and `*.local.php`
- Keep your `groq-config.local.js` and `StripeService.local.php` files locally
- For production, use environment variables

## Environment Variables (Production)

For production deployment, use environment variables:

```php
// Stripe
$secretKey = getenv('STRIPE_SECRET_KEY');
$publicKey = getenv('STRIPE_PUBLIC_KEY');

// Groq (in JavaScript, load from server-side)
const apiKey = await fetch('/api/config').then(r => r.json());
```

## Files to Configure

1. `Util/StripeService.php` - Stripe keys
2. `config/groq-config.js` - Groq API key
3. `Util/EmailService.php` - Gmail credentials (already set)

## Testing

After configuration:

1. Test Stripe: `http://localhost/NutriSmart/test-stripe.php`
2. Test Groq: `http://localhost/NutriSmart/test-chatbot.html`
3. Test Email: `http://localhost/NutriSmart/test-phpmailer.php`

---

**Important:** Keep your API keys secure and never share them publicly!
