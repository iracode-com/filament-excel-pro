<?php

namespace IracodeCom\FilamentExcelPro;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use IracodeCom\FilamentExcelPro\Commands\MakeExcelProPublishCommand;

class FilamentExcelProServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-excel-pro')
            ->hasViews('filament-excel-pro')
            ->hasMigration('create_importables_table')
            ->hasConfigFile()
            ->hasTranslations()
            ->runsMigrations()
            ->hasCommands(MakeExcelProPublishCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(fn(InstallCommand $command) => $command->info('Hello, and welcome to the excel pro package!'))
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('iracode-com/filament-excel-pro')
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Enjoy exploring new excel import experience!');
                    });
            });
    }
}