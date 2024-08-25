<?php

namespace IracodeCom\FilamentExcelPro\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
enum ImportDateTimeFormat: string implements HasLabel
{
    case SOLAR = 'solar';
    case AD    = 'ad';
    case LUNAR = 'lunar';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SOLAR => __('Solar'),
            self::AD    => __('Ad'),
            self::LUNAR => __('Lunar'),
        };
    }
}