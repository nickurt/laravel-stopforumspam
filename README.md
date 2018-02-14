## Laravel StopForumSpam

### Installation
Install this package with composer:
```
php composer.phar require nickurt/laravel-stopforumspam:1.*
```

Add the provider to config/app.php file

```php
'nickurt\StopForumSpam\ServiceProvider',
```

and the facade in the file

```php
'StopForumSpam' => 'nickurt\StopForumSpam\Facade',
```

Copy the config files for the api

```
php artisan vendor:publish --provider="nickurt\StopForumSpam\ServiceProvider" --tag="config"
```

- - - 