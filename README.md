# Clear Expired Cache files in Laravel

This package will remove any expired cache file, this only works with `file` cache driver. It will also remove any empty folder after removing the expired cache files, so your cache folder will be totally clean.

## Installation

1. Add arifhp86/laravel-clear-expired-cache-file to your project:

```bash
composer require arifhp86/laravel-clear-expired-cache-file
```

2. For **Laravel >= 5.5** we use Package Auto-Discovery, so you may skip this step.
   For **Laravel < 5.5**, add `CacheClearServiceProvider` to the providers array in config/app.php:

```php
Arifhp86\ClearExpiredCacheFile\Providers\CacheClearServiceProvider::class,
```

## Usage

```bash
php artisan cache:clear-expired
```

## License & Copyright

MIT, (c) 2019 Arifur Rahman
