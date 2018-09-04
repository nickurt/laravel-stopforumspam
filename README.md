## Laravel StopForumSpam

### Installation
Install this package with composer:
```
composer require nickurt/laravel-stopforumspam
```

Add the provider to config/app.php file

```php
'nickurt\StopForumSpam\ServiceProvider',
```

and the facade in the file

```php
'StopForumSpam' => 'nickurt\StopForumSpam\Facade',
```

Copy the config files for the StopForumSpam-plugin

```
php artisan vendor:publish --provider="nickurt\StopForumSpam\ServiceProvider" --tag="config"
```

### Examples

#### Validation Rule - IsSpamEmail
You can use a hidden-field `sfs` in your Form-Request to validate if the request is valid
```php
$validator = validator()->make(['sfs' => 'sfs'], ['sfs' => [new \nickurt\StopForumSpam\Rules\IsSpamEmail(
    request()->input('email'), 100
)]]);
```
The `IsSpamEmail` requires a `email` and an optional `frequency` parameter to validate the request.
#### Validation Rule - IsSpamIp
You can use a hidden-field `sfs` in your Form-Request to validate if the request is valid
```php
$validator = validator()->make(['sfs' => 'sfs'], ['sfs' => [new \nickurt\StopForumSpam\Rules\IsSpamIp(
    request()->ip(), 100
)]]);
```
The `IsSpamIp` requires a `ip` and an optional `frequency` parameter to validate the request.
#### Validation Rule - IsSpamUsername
You can use a hidden-field `sfs` in your Form-Request to validate if the request is valid
```php
$validator = validator()->make(['sfs' => 'sfs'], ['sfs' => [new \nickurt\StopForumSpam\Rules\IsSpamUsername(
    request()->input('username'), 100
)]]);
```
The `IsSpamUsername` requires a `username` and an optional `frequency` parameter to validate the request.
#### Manually Usage - IsSpamEmail
```php
$isSpamEmail = (new \nickurt\StopForumSpam\StopForumSpam())
	->setEmail('nickurt@users.noreply.github.com')
	->isSpamEmail();
	
// ...	
$isSpamEmail = stopforumspam()
    ->setEmail('nickurt@users.noreply.github.com')
    ->isSpamEmail();
```
#### Manually Usage - IsSpamIp
```php
$isSpamIp = (new \nickurt\StopForumSpam\StopForumSpam())
	->setIp('8.8.8.8')
	->isSpamIp();
	
// ...	
$isSpamIp = stopforumspam()
    ->setIp('8.8.8.8')
    ->isSpamIp();
```
#### Manually Usage - IsSpamUsername
```php
$isSpamUsername = (new \nickurt\StopForumSpam\StopForumSpam())
	->setUsername('nickurt')
	->isSpamUsername();
	
// ...	
$isSpamUsername = stopforumspam()
    ->setUsername('nickurt')
    ->isSpamUsername();
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
phpunit
```
- - - 