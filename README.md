## Laravel StopForumSpam
[![Build Status](https://github.com/nickurt/laravel-stopforumspam/workflows/tests/badge.svg)](https://github.com/nickurt/laravel-stopforumspam/actions)
[![Total Downloads](https://poser.pugx.org/nickurt/laravel-stopforumspam/d/total.svg)](https://packagist.org/packages/nickurt/laravel-plesk)
[![Latest Stable Version](https://poser.pugx.org/nickurt/laravel-stopforumspam/v/stable.svg)](https://packagist.org/packages/nickurt/laravel-plesk)
[![MIT Licensed](https://poser.pugx.org/nickurt/laravel-stopforumspam/license.svg)](LICENSE.md)

### Installation
Install this package with composer:
```
composer require nickurt/laravel-stopforumspam
```
Copy the config files for the StopForumSpam-plugin
```
php artisan vendor:publish --provider="nickurt\StopForumSpam\ServiceProvider" --tag="config"
```
### Examples
#### Validation Rule - IsSpamEmail
```php
// FormRequest ...

public function rules()
{
    return [
        'email' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamEmail(20)]
    ];
}

// Manually ...

$validator = validator()->make(request()->all(), ['email' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamEmail(20)]]);
```
The `IsSpamEmail`-rule has one optional paramter `frequency` (default 10) to validate the request.
#### Validation Rule - IsSpamIp
```php
// FormRequest ...

public function rules()
{
    return [
        'ip' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamIp(20)]
    ];
}

// Manually ...

$validator = validator()->make(request()->all(), ['ip' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamIp(20)]]);
```
The `IsSpamIp`-rule has one optional paramter `frequency` (default 10) to validate the request.
#### Validation Rule - IsSpamUsername
```php
// FormRequest ...

public function rules()
{
    return [
        'username' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamUsername(20)]
    ];
}

// Manually ...

$validator = validator()->make(request()->all(), ['username' => ['required', new \nickurt\StopForumSpam\Rules\IsSpamUsername(20)]]);
```
The `IsSpamUsername`-rule has one optional paramter `frequency` (default 10) to validate the request.
#### Manually Usage - IsSpamEmail
```php
\StopForumSpam::setEmail('nickurt@users.noreply.github.com')->isSpamEmail();
```
#### Manually Usage - IsSpamIp
```php
\StopForumSpam::setIp('8.8.8.8')->isSpamIp();
```
#### Manually Usage - IsSpamUsername
```php
\StopForumSpam::setUsername('nickurt')->isSpamUsername();
```
#### Events
You can listen to the `IsSpamEmail`, `IsSpamIp` and `IsSpamUsername` events, e.g. if you want to log all the `IsSpam`-requests in your application
##### IsSpamEmail Event
This event will be fired when the request-email is above the frequency of sending spam
`nickurt\StopForumSpam\Events\IsSpamEmail`
##### IsSpamIp Event
This event will be fired when the request-ip is above the frequency of sending spam
`nickurt\StopForumSpam\Events\IsSpamIp`
##### IsSpamUsername Event
This event will be fired when the request-username is above the frequency of sending spam
`nickurt\StopForumSpam\Events\IsSpamUsername`
### Tests
```sh
composer test
```
- - - 
