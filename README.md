# Filament Excel Pro

### SpartanNL/Laravel-Excel for Filament with advanced functionality

[//]: # ([![Latest Version on Packagist]&#40;https://img.shields.io/packagist/v/rmsramos/activitylog.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/rmsramos/activitylog&#41;)

[//]: # ([![Software License]&#40;https://img.shields.io/badge/license-MIT-brightgreen.svg&#41;]&#40;LICENSE.md&#41;)

[//]: # ([![GitHub Code Style Action Status]&#40;https://img.shields.io/github/actions/workflow/status/rmsramos/activitylog/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square&#41;]&#40;https://github.com/rmsramos/activitylog/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain&#41;)

[//]: # ([![Total Downloads]&#40;https://img.shields.io/packagist/dt/rmsramos/activitylog.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/rmsramos/activitylog/stats&#41;)

This package provides a Filament resource to add advanced functionality for importing data from excel files based on `SpartanNL/Laravel-Excel` package

## Requirements

-   Laravel v11
-   Filament v3
-   SpartanNL/Laravel-Excel v3

## Languages Supported

Filament Excel Pro Plugin is translated for :

-   us English
-   fa Farsi

## Installation

You can install the package via composer:

```bash
composer require iracode-com/filament-excel-pro
```

After that run the install command:

```bash
php artisan filament-excel-pro:install
```

This will publish the config & migrations & translations from `iracode-com/filament-excel-pro`

And run migrates

```bash
php artisan migrate
```

You can manually publish the configuration file with:

```bash
php artisan vendor:publish --tag="filament-excel-pro-config"
```

This is the contents of the published config file:

```php
return [
    /*
     * This model will be used to import.
     */
    'import_model' => \IracodeCom\FilamentExcelPro\Model\Importable::class,

    /*
     * This model will be determined as user model.
     * creator, updater: foreign key columns in import model for creator, updater relationships
     */
    'user'         => [
        'model'   => \App\Models\User::class,
        'creator' => 'created_by',
        'updater' => 'updated_by'
    ],

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Import model shipped with this package.
     */
    'table'        => 'importables',

    'resources' => [
        'label'                  => 'Excel Import',
        'plural_label'           => 'Excel Imports',
        'navigation_group'       => null,
        'navigation_icon'        => 'heroicon-o-clipboard-document-check',
        'navigation_sort'        => null,
        'navigation_count_badge' => false,
        'resource'               => \IracodeCom\FilamentExcelPro\Resources\ImportableResource::class,
    ],

    'datetime_format' => 'd/m/Y H:i:s',
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-excel-pro-views"
```

## Usage

### Basic SpartanNL Laravel Excel usage

In your `AppServiceProvider` add `HeadingRowFormatter::default('none')` method to disable formatting

```php
use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        HeadingRowFormatter::default('none');
    }
}
```

## Plugin usage

In your Panel ServiceProvider `(App\Providers\Filament)` active the plugin

Add the `IracodeCom\FilamentExcelPro\FilamentExcelProPlugin` to your panel config

```php
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentExcelProPlugin::make(),
        ]);
}
```

## Customising the ImportableResource

You can swap out the `ImportableResource` used by updating the `->resource()` value. Use this to create your own `CustomResource` class and extend the original at `\IracodeCom\FilamentExcelPro\Resources\ImportableResource::class`. This will allow you to customise everything such as the views, table, form and permissions.

> [!NOTE]
> If you wish to change the resource on List and View page be sure to replace the `getPages` method on the new resource and create your own version of the `ListPage` and `ViewPage` classes to reference the custom `CustomResource`.

```php
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentExcelProPlugin::make()
                ->resource(\Path\For\Your\CustomResource::class),
        ]);
}
```

## Customising label Resource

You can swap out the `Resource label` used by updating the `->label()` and `->pluralLabel()` value.

```php
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentExcelProPlugin::make()
                ->label('Excel import')
                ->pluralLabel('Excel imports'),
        ]);
}
```

## Grouping resource navigation items

You can add a `Resource navigation group` updating the `->navigationGroup()` value.

```php
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentExcelProPlugin::make()
                ->navigationGroup('Excel import'),
        ]);
}
```

## Customising a resource navigation icon

You can swap out the `Resource navigation icon` used by updating the `->navigationIcon()` value.

```php
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentExcelProPlugin::make()
                ->navigationIcon('heroicon-o-clipboard-document-check'),
        ]);
}
```

## Active a count badge

You can active `Count Badge` updating the `->navigationCountBadge()` value.

```php
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentExcelProPlugin::make()
                ->navigationCountBadge(true),
        ]);
}
```

## Set navigation sort

You can set the `Resource navigation sort` used by updating the `->navigationSort()` value.

```php
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentExcelProPlugin::make()
                ->navigationSort(3),
        ]);
}
```

## Authorization

If you would like to prevent certain users from accessing the logs resource, you should add a authorize callback in the `FilamentExcelProPlugin` chain.

```php
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentExcelProPlugin::make()
                ->authorize(
                    fn () => auth()->user()->id === 1
                ),
        ]);
}
```

## Full configuration

```php
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentExcelProPlugin::make()
                ->resource(\Path\For\Your\CustomResource::class)
                ->label('Excel import')
                ->pluralLabel('imports')
                ->navigationGroup('Excel import')
                ->navigationIcon('heroicon-o-shield-check')
                ->navigationCountBadge(true)
                ->navigationSort(2)
                ->authorize(
                    fn () => auth()->user()->id === 1
                ),
        ]);
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Acknowledgements

Special acknowledgment goes to these remarkable tools and people (developers), the Excel import plugin only exists due to the inspiration and at some point the use of these people's codes.

-   [Filament](https://github.com/filamentphp/filament)

## Credits

-   [ArdavanShamroshan](.com/Ardavan-Shamroshan)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
