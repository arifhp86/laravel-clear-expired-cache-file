# Clear Expired Cache Files

[![Latest Stable Version](https://img.shields.io/github/release/arifhp86/laravel-clear-expired-cache-file.svg?style=flat-square)](https://github.com/arifhp86/laravel-clear-expired-cache-file/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/arifhp86/laravel-clear-expired-cache-file.svg?style=flat-square)](https://packagist.org/packages/arifhp86/laravel-clear-expired-cache-file)

When you use the `file` cache driver, you may have noticed that the cache files are never deleted automatically. They are overwritten when the cache is refreshed which is good, but if there is some randomness in you cache keys, or for some other reason some of your cache files are never touched again, they will stay on your server forever. This package helps you to get rid of these lingering files.

This package only adds one artisan command: `php artisan cache:clear-expired` and it will delete all cache files that has already been expired.

## Installation

You can install the package via composer:

```bash
composer require arifhp86/laravel-clear-expired-cache-file
```

## Usage

Run the following command
```bash
php artisan cache:clear-expired
```
### Run on schedule
You can also schedule the command to run automatically. For example, if you want to run the command every day at 1 AM, you can add the following to your `app/Console/Kernel.php` file:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('cache:clear-expired')->dailyAt('01:00');
}
```

### Dry run
You can run the command in dry run mode. In this mode, the command will not delete any files. It will only show you the files that will be deleted if you run the command without the `--dry-run` option.

```bash
php artisan cache:clear-expired --dry-run
```

### Disable directory deletion
By default, the command will delete all empty directories after deleting the expired files. You can disable this behavior by using the `--disable-directory-delete` option.

```bash
php artisan cache:clear-expired --disable-directory-delete
```

### Events
The command triggers three events:
- `Arifhp86\ClearExpiredCacheFile\Events\GarbageCollectionStarting`: Before the command runs.
- `Arifhp86\ClearExpiredCacheFile\Events\GarbageCollectionEnded`: After the command runs.
- `Arifhp86\ClearExpiredCacheFile\Events\GarbageCollectionFailed`: If the command fails.

You can use these events to run your own code before or after the command runs.
#### Send email notification after the command runs

```php
// app/Providers/EventServiceProvider.php boot method

use Arifhp86\ClearExpiredCacheFile\Events\GarbageCollectionEnded;

Event::listen(function (GarbageCollectionEnded $event) {
    $timeTaken = $event->time; // in seconds
    $memory = $event->memory; // in bytes
    $numberOfFilesDeleted = $event->expiredFiles->getCount();
    $diskCleared = $event->expiredFiles->getFormattedSize();
    $remainingFiles = $event->activeFiles->getCount();
    $remainingDisk = $event->activeFiles->getFormattedSize();
    $numberOfDeletedDirectories = $event->deletedDirectories;
    
    // Send email notification
    //...
});
```

#### Send email notification if the command fails

```php
// app/Providers/EventServiceProvider.php boot method

use Arifhp86\ClearExpiredCacheFile\Events\GarbageCollectionFailed;

Event::listen(function (GarbageCollectionFailed $event) {
    $exception = $event->exception;
    $message = $exception->getMessage();
    $stackTrace = $exception->getTraceAsString();
    
    // Send email notification
    //...
});
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Arifur Rahman](https://github.com/arifhp86)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
