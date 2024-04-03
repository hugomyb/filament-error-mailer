# Filament Error Mailer 🚨

Filament plugin for instant e-mail alerts on web errors, simplifying monitoring and application stability.

## Installation

You can install the package via composer:

```bash
composer require hugomyb/filament-error-mailer
```

Then, publish the config file with:

```bash
php artisan vendor:publish --tag="error-mailer-config"
```

This will create a `config/error-mailer.php` file in your Laravel project.

This is the contents of the published config file:

```php
return [
    'email' => [
        'recipient' => ['recipient1@example.com'],
        'bcc' => [],
        'cc' => [],
        'subject' => 'An error was occured - ' . env('APP_NAME'),
    ],

    'disabledOn' => [
        //
    ],

    'cacheCooldown' => 10, // in minutes
];
```

Optionally, you can publish the mail view using:

```bash
php artisan vendor:publish --tag="error-mailer-views"
```

## Configuration

After publishing the configuration file, you can modify it to suit your needs. Open `config/error-mailer.php` and
customize the following options:

- `'recipient'`: Set email addresses where error notifications will be sent.

- `'bcc'`: Set email addresses where error notifications will be sent in BCC.

- `'cc'`: Set email addresses where error notifications will be sent in CC.

- `'subject'`: Define the subject line for error notification emails. You can use placeholders like `env('APP_NAME')` to
dynamically include your application's name.

- `'cacheCooldown'`: Set the cooling-off period (in minutes) for error notifications. If the same error occurs several times within this period

- `'disabledOn'`: You can specify a list of environments (based on `APP_ENV`) where the Error Mailer will be disabled.
For example, if you want to disable the mailer in the local environment, add 'local' to the array:

```php
'disabledOn' => [
    'local',
],
```

<hr/>

> ⚠️ **IMPORTANT ! Make sure to configure a mail server in your `.env` file :**

```sh
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
```

If the mail server is not configured in the `.env` file, email notifications will not be sent.

<hr>

Finally, don't forget to register the plugin in your `AdminPanelProvider`:

```php
...
->plugins([
    FilamentErrorMailerPlugin::make()
])
``` 
## More

This plugin is also available for a classic Laravel project without FilamentPHP : **[LaravelErrorMailer](https://github.com/hugomayo7/LaravelErrorMailer)**

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mayonobe Hugo](https://github.com/hugomyb)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
