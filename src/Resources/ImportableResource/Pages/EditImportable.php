<?php

namespace IracodeCom\FilamentExcelPro\Resources\ImportableResource\Pages;

use IracodeCom\FilamentExcelPro\Resources\ImportableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportable extends EditRecord
{
    protected static string $resource = ImportableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
