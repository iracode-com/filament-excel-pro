<?php

namespace IracodeCom\FilamentExcelPro\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum ImportMethod: int implements HasLabel, HasDescription
{
    case IMPORT_ALL                     = 0;
    case UPDATE_EXISTING                = 1;
    case IMPORT_NEW_AND_UPDATE_EXISTING = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::IMPORT_ALL                     => __('Import all data as new'),
            self::UPDATE_EXISTING                => __('Update only existing data'),
            self::IMPORT_NEW_AND_UPDATE_EXISTING => __('Update existing data and import the rest'),
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::IMPORT_ALL                     => __('In this method, the rows of your file are imported in the corresponding module. If needed, you can select a field as a control criterion for duplicate records so that the system recognizes duplicate files with existing files and does not import them.'),
            self::UPDATE_EXISTING                => __('In this method, only data from your file is imported into the relevant module that is present in your records. In order for Dana to recognize which record is duplicate, you must select a field as the criterion of duplicateness.'),
            self::IMPORT_NEW_AND_UPDATE_EXISTING => __('This method is a combination of the above two methods, that is, if the file data does not exist in the corresponding module, it creates it, and if it exists, it updates the information of that record. For this reason, the method of detecting duplicate records is mandatory and must be specified.'),
        };
    }
}