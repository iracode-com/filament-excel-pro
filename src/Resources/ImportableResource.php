<?php

namespace IracodeCom\FilamentExcelPro\Resources;

use IracodeCom\FilamentExcelPro\Resources\ImportableResource\Pages;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Filament\Forms\Components\MorphToSelect;
use Illuminate\Support\Str;
use IracodeCom\FilamentExcelPro\FilamentExcelProPlugin;
use IracodeCom\FilamentExcelPro\Model\Importable;

class ImportableResource extends Resource
{
    public static function getModel(): string
    {
        return config('filament-excel-pro.import_model') ?? Importable::class;
    }

    public static function getModelLabel(): string
    {
        return FilamentExcelProPlugin::get()->getLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return FilamentExcelProPlugin::get()->getPluralLabel();
    }

    public static function getNavigationIcon(): string
    {
        return FilamentExcelProPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return Str::title(static::getPluralModelLabel()) ?? Str::title(static::getModelLabel());
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentExcelProPlugin::get()->getNavigationSort();
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentExcelProPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationBadge(): ?string
    {
        return FilamentExcelProPlugin::get()->getNavigationCountBadge() ? number_format(static::getModel()::count()) : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Pages\CreateImportable::schema())
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(Pages\ListImportables::schema())
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListImportables::route('/'),
            'create' => Pages\CreateImportable::route('/create'),
            'import' => Pages\ImportImportable::route('/import/{importable:name}'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
