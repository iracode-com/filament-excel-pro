<?php

namespace IracodeCom\FilamentExcelPro\Resources\ImportableResource\Pages;

use IracodeCom\FilamentExcelPro\Resources\ImportableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class ListImportables extends ListRecords
{
    protected static string $resource = ImportableResource::class;

    public static function schema(): array
    {
        return [
            TextColumn::make('name')->limit(15)->tooltip(fn($state) => $state),
            TextColumn::make('type')->limit(15)->tooltip(fn($state) => $state)->searchable(),
            TextColumn::make('path')->limit(15)->tooltip(fn($state) => $state),
            TextColumn::make('header')->listWithLineBreaks()->limitList(1)->expandableLimitedList()->limit(15),
            TextColumn::make('records_count')->suffix(__('Item')),
            TextColumn::make('method')->badge()->color('info')->sortable(),
            TextColumn::make('date_time_format')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('type')->limit(15)->tooltip(fn($state) => $state)->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('size')->formatStateUsing(fn($state) => Number::fileSize($state)),
            TextColumn::make('step')->badge(),
            TextColumn::make('created_at')->dateTime()->sortable()->formatStateUsing(fn($state) => verta($state))->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')->dateTime()->sortable()->formatStateUsing(fn($state) => verta($state))->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('deleted_at')->dateTime()->sortable()->formatStateUsing(fn($state) => verta($state))->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
