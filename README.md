# Filament Error Mailer 🚨

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hugomyb/filament-error-mailer.svg?style=flat-square)](https://packagist.org/packages/hugomyb/filament-error-mailer)
[![Total Downloads](https://img.shields.io/packagist/dt/hugomyb/filament-error-mailer.svg?style=flat-square)](https://packagist.org/packages/hugomyb/filament-error-mailer)

A powerful Filament plugin that provides instant error notifications via email and Discord webhooks, with a beautiful error details page for debugging. Never miss a critical error in your application again!

## ✨ Key Features

- 📧 **Instant Email Notifications** - Get notified immediately when errors occur
- 💬 **Discord Webhook Integration** - Send alerts to your Discord channels
- 🔗 **Custom Webhook Endpoints** - POST raw error JSON to any number of URLs (n8n, Zapier, Slack apps, custom APIs)
- 🎯 **Smart Application File Detection** - Automatically identifies errors in your code (excluding vendor files)
- 🌓 **Beautiful Error Details Page** - Dark/Light mode with copy & share functionality
- ⏱️ **Cooldown System** - Prevents notification spam for duplicate errors
- 🎛️ **Advanced Filtering** - Filter by log level, exception type, or environment
- 🔒 **Secure Access** - Protected by Filament authentication
- 📦 **JSON Storage** - All errors stored as JSON files for easy access

## 📋 Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
  - [Email Configuration](#email-configuration)
  - [Discord Webhook](#discord-webhook)
  - [Custom Webhook Endpoints](#custom-webhook-endpoints)
  - [Error Filtering](#error-filtering)
  - [Cooldown Period](#cooldown-period)
  - [Storage Path](#storage-path)
- [Features in Detail](#features-in-detail)
  - [Smart Application File Detection](#smart-application-file-detection)
  - [Error Details Page](#error-details-page)
  - [Notification Cooldown](#notification-cooldown)
- [Usage](#usage)
  - [Accessing Error Details](#accessing-error-details)
  - [Scheduled Cleanup](#scheduled-cleanup)
- [Advanced Configuration](#advanced-configuration)
- [Contributing](#contributing)
- [License](#license)

## 📦 Installation

### Step 1: Install via Composer

```bash
composer require hugomyb/filament-error-mailer
```

### Step 2: Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="error-mailer-config"
```

This creates `config/error-mailer.php` with the following default configuration:

```php
return [
    'email' => [
        'recipient' => ['recipient1@example.com'],
        'bcc' => [],
        'cc' => [],
        'subject' => 'An error has occurred - ' . config('app.name'),
    ],

    'disabledOn' => [
        //
    ],

    'cacheCooldown' => 10, // in minutes

    'webhooks' => [
        'discord' => env('ERROR_MAILER_DISCORD_WEBHOOK'),

        // Additional generic webhook URLs the plugin will POST the raw
        // error details JSON to. Can also be set via env (CSV).
        'endpoints' => array_values(array_filter(array_map('trim', explode(',', (string) env('ERROR_MAILER_WEBHOOK_URLS', ''))))),

        'message' => [
            'title' => 'Error Alert - ' . config('app.name'),
            'description' => 'An error has occurred in the application.',
            'error' => 'Error',
            'file' => 'File',
            'line' => 'Line',
            'details_link' => 'See more details'
        ],
    ],

    'storage_path' => storage_path('app/errors'),

    'ignore' => [
        'levels' => [
            // 'debug',
            // 'info',
        ],
        'exceptions' => [
            // \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        ],
    ],
];
```

### Step 3: Configure Mail Server

> ⚠️ **IMPORTANT**: Configure your mail server in `.env` to receive email notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourapp.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 4: Register the Plugin

Add the plugin to your Filament panel provider (e.g., `app/Providers/Filament/AdminPanelProvider.php`):

```php
use Hugomyb\FilamentErrorMailer\FilamentErrorMailerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other configuration
        ->plugins([
            FilamentErrorMailerPlugin::make(),
        ]);
}
```

### Step 5: (Optional) Publish Views

If you want to customize the error details page or email template:

```bash
php artisan vendor:publish --tag="error-mailer-views"
```

---

## ⚙️ Configuration

### Email Configuration

Configure email recipients and subject in `config/error-mailer.php`:

```php
'email' => [
    'recipient' => ['admin@example.com', 'dev@example.com'],
    'bcc' => ['monitoring@example.com'],
    'cc' => [],
    'subject' => 'Error Alert - ' . config('app.name'),
],
```

**Options:**
- `recipient` (array): Primary email addresses to receive notifications
- `bcc` (array): Blind carbon copy recipients
- `cc` (array): Carbon copy recipients
- `subject` (string): Email subject line (supports dynamic values)

### Discord Webhook

Send error notifications to Discord channels:

**1. Create a Discord Webhook:**
- Go to your Discord server settings
- Navigate to Integrations → Webhooks
- Click "New Webhook"
- Copy the webhook URL

**2. Add to `.env`:**

```env
ERROR_MAILER_DISCORD_WEBHOOK="https://discord.com/api/webhooks/your-webhook-id/your-webhook-token"
```

**3. Customize webhook messages (optional):**

```php
'webhooks' => [
    'discord' => env('ERROR_MAILER_DISCORD_WEBHOOK'),

    'message' => [
        'title' => 'Error Alert - ' . config('app.name'),
        'description' => 'An error has occurred in the application.',
        'error' => 'Error',
        'file' => 'File',
        'line' => 'Line',
        'details_link' => 'View Details',
    ],
],
```

### Custom Webhook Endpoints

In addition to the Discord webhook, the plugin can POST the raw error details (as JSON) to any number of custom URLs. This is useful for forwarding errors to **n8n**, **Zapier**, **Slack apps**, a monitoring service, or your own internal API.

**1. Via `.env` (comma-separated list):**

```env
ERROR_MAILER_WEBHOOK_URLS="https://hooks.example.com/error,https://n8n.example.com/webhook/abc123"
```

**2. Or directly in `config/error-mailer.php`:**

```php
'webhooks' => [
    'discord' => env('ERROR_MAILER_DISCORD_WEBHOOK'),

    'endpoints' => [
        'https://hooks.example.com/error',
        'https://n8n.example.com/webhook/abc123',
        env('CUSTOM_MONITORING_WEBHOOK'),
    ],

    // ...
],
```

Each URL receives a `POST` request with a JSON body containing the full error details:

```json
{
    "id": "a1b2c3d4e5f6...",
    "message": "Undefined variable $foo",
    "file": "/app/Http/Controllers/UserController.php",
    "line": 42,
    "appFile": "/app/Http/Controllers/UserController.php",
    "appLine": 42,
    "url": "https://yourapp.com/users",
    "method": "GET",
    "ip": "1.2.3.4",
    "userAgent": "Mozilla/5.0 ...",
    "referrer": "https://yourapp.com/",
    "requestTime": "2026-06-08 14:23:55",
    "requestUri": "/users",
    "authUser": { "id": 1, "name": "Jane", "email": "jane@example.com" },
    "stackTrace": "...",
    "last_notified_at": "2026-06-08 14:23:55",
    "details_url": "https://yourapp.com/admin/error/a1b2c3d4...",
    "app_name": "MyApp",
    "app_url": "https://yourapp.com"
}
```

Endpoints share the same cooldown as the email/Discord notifications — duplicate errors within `cacheCooldown` minutes are not re-sent.

### Error Filtering

Control which errors trigger notifications:

```php
'ignore' => [
    // Ignore specific log levels
    'levels' => [
        'debug',
        'info',
        // 'warning',
        // 'error',
    ],

    // Ignore specific exception types
    'exceptions' => [
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Auth\AuthenticationException::class,
    ],
],
```

**Available log levels:**
- `debug` - Detailed debug information
- `info` - Interesting events
- `notice` - Normal but significant events
- `warning` - Exceptional occurrences that are not errors
- `error` - Runtime errors
- `critical` - Critical conditions
- `alert` - Action must be taken immediately
- `emergency` - System is unusable

### Disable in Specific Environments

Prevent notifications in certain environments (e.g., local development):

```php
'disabledOn' => [
    'local',
    'testing',
],
```

### Cooldown Period

Prevent notification spam for duplicate errors:

```php
'cacheCooldown' => 10, // in minutes
```

If the same error occurs multiple times within this period, only the first occurrence will trigger a notification.

### Storage Path

Customize where error JSON files are stored:

```php
'storage_path' => storage_path('app/errors'),
```

---

## 🎯 Features in Detail

### Smart Application File Detection

When an error occurs, the package intelligently identifies the **first line of code from your application** (excluding vendor files) in the stack trace.

**Example:**

Instead of showing:
```
File: /vendor/laravel/framework/src/Illuminate/Database/Connection.php
Line: 742
```

You'll see:
```
Application File: /app/Http/Controllers/UserController.php  ← Your code!
Application Line: 25                                         ← Your code!
Origin File: /vendor/laravel/framework/src/Illuminate/Database/Connection.php
Origin Line: 742
```

This makes debugging **significantly faster** by immediately showing you where in **your code** the error originated.

### Error Details Page

Each error notification includes a unique link to a beautiful, feature-rich error details page:

**Features:**
- 🌓 **Dark/Light Mode** - Toggle themes with persistent preference (saved in localStorage)
- 📋 **Copy as Markdown** - Copy formatted error details for documentation
- 📄 **Copy as JSON** - Copy raw error data for processing
- 🔗 **Share** - Use native Web Share API (mobile-friendly)
- 🔒 **Secure** - Protected by Filament authentication
- 📱 **Responsive** - Works perfectly on all devices

**Information displayed:**
- Error message and exception type
- Application file and line (your code)
- Origin file and line (where exception was thrown)
- Full stack trace
- Request details (method, URL, IP, user agent, referrer)
- Authenticated user information (if available)
- Timestamp

**Access:** Only authenticated Filament users can view error details.

### Notification Cooldown

The cooldown system prevents notification spam:

1. When an error occurs, a notification is sent
2. Error details are stored with a timestamp
3. If the same error occurs again within the cooldown period, no new notification is sent
4. After the cooldown expires, the next occurrence will trigger a new notification

**Error identification:** Errors are identified by a hash of the error message and file path.

---

## 🚀 Usage

### Accessing Error Details

Error detail links are automatically included in:
- Email notifications
- Discord webhook messages

**URL format:** `https://yourapp.com/error-mailer/{errorId}`

**Example email:**
```
Subject: Error Alert - MyApp

An error has occurred:
Message: Call to undefined method...
File: /app/Http/Controllers/UserController.php
Line: 25

View full details: https://yourapp.com/error-mailer/abc123def456
```

### Scheduled Cleanup

Error JSON files are stored indefinitely by default. To prevent excessive storage usage, schedule a cleanup task in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Delete errors older than 3 months
    $schedule->call(function () {
        $storagePath = config('error-mailer.storage_path');
        $files = File::files($storagePath);

        foreach ($files as $file) {
            if ($file->getMTime() < now()->subMonths(3)->timestamp) {
                File::delete($file->getRealPath());
            }
        }
    })->daily();
}
```

**Recommended retention periods:**
- Production: 3-6 months
- Staging: 1-3 months
- Development: 1 month

---

## 🔧 Advanced Configuration

### Complete Configuration Reference

```php
return [
    // Email notification settings
    'email' => [
        'recipient' => ['admin@example.com'],
        'bcc' => [],
        'cc' => [],
        'subject' => 'Error Alert - ' . config('app.name'),
    ],

    // Environments where notifications are disabled
    'disabledOn' => [
        // 'local',
        // 'testing',
    ],

    // Cooldown period in minutes
    'cacheCooldown' => 10,

    // Webhook configuration
    'webhooks' => [
        'discord' => env('ERROR_MAILER_DISCORD_WEBHOOK'),

        // Additional URLs to POST the raw error JSON to.
        'endpoints' => array_values(array_filter(array_map('trim', explode(',', (string) env('ERROR_MAILER_WEBHOOK_URLS', ''))))),

        'message' => [
            'title' => 'Error Alert - ' . config('app.name'),
            'description' => 'An error has occurred in the application.',
            'error' => 'Error',
            'file' => 'File',
            'line' => 'Line',
            'details_link' => 'View Details',
        ],
    ],

    // Storage path for error JSON files
    'storage_path' => storage_path('app/errors'),

    // Error filtering
    'ignore' => [
        'levels' => [
            // 'debug',
            // 'info',
        ],
        'exceptions' => [
            // \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        ],
    ],
];
```

---

## 📚 Related Projects

This plugin is also available for **Laravel projects without Filament**:

👉 **[Laravel Error Mailer](https://github.com/hugomayo7/LaravelErrorMailer)**

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Development Setup

```bash
# Clone the repository
git clone https://github.com/hugomyb/filament-error-mailer.git
cd filament-error-mailer

# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage
composer test-coverage
```

### Running Tests

```bash
# Run all tests
vendor/bin/pest

# Run specific test file
vendor/bin/pest tests/Unit/ErrorDetailsBuilderTest.php

# Run with coverage
vendor/bin/pest --coverage
```

---

## 🔒 Security Vulnerabilities

If you discover a security vulnerability, please send an email to [hugomayonobe@gmail.com](mailto:hugomayonobe@gmail.com). All security vulnerabilities will be promptly addressed.

Please review [our security policy](../../security/policy) for more information.

---

## 👥 Credits

- [Hugo Mayonobe](https://github.com/hugomyb) - Creator & Maintainer
- [All Contributors](../../contributors)

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## 💡 Support

If you find this package helpful, please consider:
- ⭐ Starring the repository
- 🐛 Reporting bugs or suggesting features via [GitHub Issues](https://github.com/hugomyb/filament-error-mailer/issues)
- 📖 Improving documentation via pull requests

---

**Made with ❤️ for the Filament community**
