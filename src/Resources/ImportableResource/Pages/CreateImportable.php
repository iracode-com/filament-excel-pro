<?php

namespace IracodeCom\FilamentExcelPro\Resources\ImportableResource\Pages;

use IracodeCom\FilamentExcelPro\Enums\ImportStep;
use IracodeCom\FilamentExcelPro\Resources\ImportableResource;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\HeadingRowImport;
use Filament\Forms;
use IracodeCom\FilamentExcelPro\Imports\BaseImport;
use IracodeCom\FilamentExcelPro\Services\ImportableService;

class CreateImportable extends CreateRecord
{
    protected static string $resource = ImportableResource::class;

    public static function schema(): array
    {
        return [
            Forms\Components\Section::make([
                Forms\Components\Select::make('importable_type')
                    ->live()
                    ->native(false)
                    ->options(function () {
                        $resources = Filament::getResources();
                        $models    = Arr::flatten(
                            Arr::map($resources, fn($resource) => Arr::prepend([], app($resource)::getModel()))
                        );

                        $modelNames = Arr::map($models, fn($model) => __(str($model)->afterLast('\\')->value()));

                        return array_combine($models, $modelNames);
                    }),

                FileUpload::make('import')
                    ->hiddenLabel()
                    ->helperText(__('Import data into database from excel file'))
                    ->directory(fn(Forms\Get $get) => app(Filament::getModelResource($get('importable_type')))::getSlug())
                    ->required()
                    ->rules('mimes:xls,xlsx')
                    ->live()
                    ->visible(fn(Forms\Get $get) => $get('importable_type')),
            ])
        ];
    }

    public function create(bool $another = false): void
    {
        $data     = $this->form->getState();
        $filename = $data['import'];

        if ($filename) {
            $filePath   = storage_path('app/public/' . $filename);
            $fileObject = new UploadedFile($filePath, 'public');
            $resource   = Filament::getModelResource($data['importable_type']);

            $foreignKeys = method_exists($data['importable_type'], 'getForeignKeys')
                ? app($data['importable_type'])->getForeignKeys()
                : null;


            $file =  app(ImportableService::class)->createNewImportable([
                'importable_type'     => $data['importable_type'],
                'importable_resource' => $resource,
                'name'                => $fileObject->getFilename(),
                'path'                => $filename,
                'size'                => $fileObject->getSize(),
                'type'                => $fileObject->getMimeType(),
                'header'              => array_filter(Arr::flatten((new HeadingRowImport)->toArray($filePath))),
                'data'                => array_filter(Arr::first((new BaseImport())->toArray($filePath))),
                'foreign_keys'        => $foreignKeys,
                'records_count'       => count(array_filter(Arr::first((new BaseImport())->toArray($filePath)))),
                'step'                => ImportStep::UPLOADED
            ]);

            Notification::make()->title(__('Saved.'))->success()->send();

            $this->redirect($this->getResource()::getUrl('import', [
                'importable' => $file
            ]), true);
        }
    }
}
