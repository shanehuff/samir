## Provider

Add module provider to `config/app.php`
```php
    /**
     * Module Service providers...
     */
    Modules\ThreeCommas\Providers\ThreeCommasProvider::class,
```

## Configurations

```
php artisan vendor:publish --provider="Modules\ThreeCommas\Providers\ThreeCommasProvider" --tag="config"
```
## Todo

- Module's Custom Exceptions