# Password Expiry for Laravel

Password expiry
- It allows you to set user password to expire in x days after creating/resetting.
- Using imanghafoori/laravel-password-history, user cannot use the last x previous paswords.
- Can use as middleware and validation
- It does support Laravel 9.x - 11.x

## Install

Via Composer

``` bash
$ composer require zenepay/password-expiry
```

You need to migrate you database.

```bash
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Zenepay\PasswordExpiry\PasswordExpiryServiceProvider" --tag="config"
```

When published, the `config/password_history.php` config file contains:

```php
return [

      'expiry_days' => 90
];
```

You can change it according to your needs.

## Usage
* Include Following trait in User Model
```php

use Zenepay\PasswordExpiry\Traits\PasswordExpirable;

class User extends Authenticatable {
    use PasswordExpirable;
}
```


* You can check if user password is expired?
``` php
$user->isPasswordExpired();
```

* You can protect your routes from user with expired password
by :
# For Laravel < 11
add following middleware to app/Http/Kernel.php
- To prevent user with password expire to access page.
This will redirect to reset password page
```php
use Zenepay\PasswordExpiry\CheckPasswordExpired;

protected $routeMiddleware = [
    ...
    'check-password-expired' => CheckPasswordExpired::class
]
```
# For Laravel 11
Add this to bootstrap/app.php
```php
use Zenepay\PasswordExpiry\CheckPasswordExpired;

->withMiddleware(function (Middleware $middleware) {
     $middleware->append(CheckPasswordExpired::class);
})

```
# For Laravel + Filament 3
For Laravel Filament 3, you can put to panel middleware
```php
use Zenepay\PasswordExpiry\CheckPasswordExpired;

   $panel->authMiddleware([
        ...,
        CheckPasswordExpired::class
    ])
```
## Validate to prevent using previous passwords
In any validate for password rule, add NoPreviousPassword::ofUser($user) in

```php
use Zenepay\PasswordExpiry\Rules\NoPreviousPassword;

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            ...
            'password' => ['required', 'confirmed',
            Rules\Password::defaults(),
            NoPreviousPassword::ofUser($request->user())],
        ]);
    }
```
In Filament with Breezy plugin and Profile page you can add rule as below
```php
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Zenepay\PasswordExpiry\Rules\NoPreviousPassword;
    $panel->plugins([
        BreezyCore::make()
        ->passwordUpdateRules(
            rules: [Password::default()->mixedCase()->uncompromised(3),NoPreviousPassword::ofUser(Auth::user())],
            requiresCurrentPassword: true,
        )
    ])
```
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Iman]https://github.com/imanghafoori1 for his great password history package
- [Fahad Ali]https://github.com/fahad-larasoft for is an inspireation of password-expirable package
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
