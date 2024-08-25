<?php

namespace IracodeCom\FilamentExcelPro\Model;

use IracodeCom\FilamentExcelPro\Enums\ImportDateTimeFormat;
use IracodeCom\FilamentExcelPro\Enums\ImportMethod;
use IracodeCom\FilamentExcelPro\Enums\ImportStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Importable extends Model
{
    protected $fillable = ['name', 'created_by', 'updated_by', 'path', 'importable_type', 'importable_resource', 'records_count', 'header', 'parsed_header', 'data', 'parsed_data', 'foreign_keys', 'type', 'size', 'method', 'duplicates', 'date_time_format', 'step'];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->table)) {
            $this->setTable(config('filament-excel-pro.table'));
        }

        parent::__construct($attributes);
    }

    protected function casts(): array
    {
        return [
            'header'           => 'array',
            'parsed_header'    => 'array',
            'data'             => 'array',
            'parsed_data'      => 'array',
            'duplicates'       => 'array',
            'foreign_keys'     => 'array',
            'step'             => ImportStep::class,
            'date_time_format' => ImportDateTimeFormat::class,
            'method'           => ImportMethod::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            config('filament-excel-pro.user.model'),
            config('filament-excel-pro.user.creator')
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            config('filament-excel-pro.user.model'),
            config('filament-excel-pro.user.updater')
        );
    }
}