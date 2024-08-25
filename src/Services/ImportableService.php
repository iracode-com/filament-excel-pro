<?php

namespace IracodeCom\FilamentExcelPro\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use IracodeCom\FilamentExcelPro\Model\Importable;

class ImportableService
{
    protected static ?string $model = Importable::class;

    public function createNewImportable(array $attributes): Model
    {
        return static::$model::query()->updateOrCreate([
            'size' => $attributes['size'],
            'name' => $attributes['name'],
        ], $attributes);

    }

    public function update(array $attributes, Importable $importable): JsonResponse
    {
        $importable->update($attributes);
        return response()->json($importable);
    }
}