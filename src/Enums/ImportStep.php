<?php

namespace IracodeCom\FilamentExcelPro\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ImportStep: int implements HasLabel, HasIcon, HasColor
{
    case NONE          = 0;
    case UPLOADED      = 1;
    case HEADER_PARSED = 2;
    case DATA_PARSED   = 3;
    case IMPORTED      = 4;
    case FAILED        = 5;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NONE          => __('Undefined'),
            self::UPLOADED      => __('Uploaded'),
            self::HEADER_PARSED => __('Header parsed'),
            self::DATA_PARSED   => __('Data parsed'),
            self::IMPORTED      => __('IMPORTED'),
            self::FAILED        => __('Failed to load'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::NONE                             => '',
            self::UPLOADED                         => 'heroicon-o-arrow-up-on-square-stack',
            self::HEADER_PARSED, self::DATA_PARSED => 'heroicon-o-arrow-path',
            self::IMPORTED                         => 'heroicon-o-clipboard-document-check',
            self::FAILED                           => 'heroicon-o-x-circle',

        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NONE                             => 'gray',
            self::UPLOADED                         => 'info',
            self::HEADER_PARSED, self::DATA_PARSED => 'warning',
            self::IMPORTED                         => 'success',
            self::FAILED                           => 'danger',
        };
    }
}