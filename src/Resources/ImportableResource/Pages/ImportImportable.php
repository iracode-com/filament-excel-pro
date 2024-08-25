<?php

namespace IracodeCom\FilamentExcelPro\Resources\ImportableResource\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use IracodeCom\FilamentExcelPro\Enums;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use IracodeCom\FilamentExcelPro\Import\Import;
use IracodeCom\FilamentExcelPro\Resources\ImportableResource;
use IracodeCom\FilamentExcelPro\Traits\InteractsWithImport;
use IracodeCom\FilamentExcelPro\Model\Importable;
use IracodeCom\FilamentExcelPro\Services\ImportableService;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ImportImportable extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = ImportableResource::class;
    protected static string $view     = 'filament-excel-pro::pages.import-importable';

    public ?Importable $file;
    public array       $data    = [];
    public array       $options = [];
    public array       $rules   = [];
    public array       $excepts = [];

    public function getTitle(): string|Htmlable
    {
        return __('Excel import');
    }

    public function mount(Importable $importable): void
    {
        $this->file = $importable;
        $this->form->fill();
        $resource      = app($this->file->importable_resource);
        $options       = app($resource::getModel())->getFillable();
        $translated    = Arr::map($options, fn($item) => __(str($item)->headline()->lower()->ucfirst()->value()));
        $this->options = array_combine($options, $translated);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(__('Settings'))
                        ->description(__('Select import method'))
                        ->schema([
                            Forms\Components\Radio::make('method')
                                ->required()
                                ->label(__('Select import method'))
                                ->options(Enums\ImportMethod::class)
                                ->default(Enums\ImportMethod::IMPORT_ALL),

                            Forms\Components\Group::make([
                                Forms\Components\Select::make('duplicates')
                                    ->label(__('Duplicate records control using'))
                                    ->options(fn() => $this->options)
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Radio::make('date_time_format')
                                    ->required()
                                    ->options(Enums\ImportDateTimeFormat::class)
                                    ->default(Enums\ImportDateTimeFormat::SOLAR)
                            ])->columnSpan(1)
                        ])
                        ->statePath('settings')
                        ->columns(),

                    Forms\Components\Wizard\Step::make(__('Corresponding keys'))
                        ->description(__('Select the corresponding Excel file keys carefully.'))
                        ->schema([
                            Forms\Components\Tabs::make('keys')->contained(false)->schema([
                                Forms\Components\Tabs\Tab::make(__('Corresponding keys'))->schema(fn() => $this->headersSchemaBuilder($this->file)),
                                Forms\Components\Tabs\Tab::make(__('Default values'))->schema([
                                    Forms\Components\Repeater::make('default_values')
                                        ->schema([
                                            Forms\Components\Select::make('key')->options(fn() => $this->options),
                                            Forms\Components\TextInput::make('value'),
                                        ])->columns()
                                ]),
                            ])->columnSpanFull()
                        ])
                        ->columns(3)
                        ->statePath('headers'),
                ])
                    ->skippable(false)
                    ->columnSpanFull()
                    ->persistStepInQueryString(),
            ])->statePath('data');
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(Importable::query()->where('name', $this->file->name))
            ->columns(ListImportables::schema())
            ->paginated(false);
    }

    public function create(): void
    {
        $data          = $this->form->getState();
        $header        = $this->file->header;
        $foreignKeys   = $this->file->foreign_keys;
        $defaultValues = $data['headers']['default_values'];
        $fillables     = array_filter($data['headers'], 'is_numeric');
        Arr::forget($fillables, 'default_values');

        $parsedHeaders = Arr::collapse(
            Arr::map($fillables, fn(?string $value, ?string $key) => ! key_exists($value, $header)
                ?: Arr::prepend([], $header[$value], $key))
        );

        $this->file->update(
            array_merge(
                $data['settings'],
                ['parsed_header' => $parsedHeaders, 'step' => Enums\ImportStep::HEADER_PARSED]
            )
        );

        $parsedData = $this->parseData($this->file->data, $this->file->parsed_header, $defaultValues);
        app(ImportableService::class)->update(['parsed_data' => $parsedData, 'step' => Enums\ImportStep::DATA_PARSED], $this->file);

        $errors = [];
        foreach ($this->file->parsed_data as $item) {
            // if ($foreignKeys) {
            //     foreach ($foreignKeys as $key => $relatedModel) {
            //         $item[$key] = app($relatedModel)->whereAny(['name', 'name_en'], $item[$key])->firstOrCreate()->id;
            //     }
            // }

            try {
                app($this->file->importable_type)::create($item);
            } catch (\Exception $e) {
                $errors[] = [
                    'record'    => $item,
                    'exception' => $e->getMessage()
                ];
            }
        }

        if (count($errors)) {
            $this->file->update(['step' => Enums\ImportStep::FAILED]);
            Notification::make()->title(__('An error occurred while uploading the file.'))->danger()->send();
            dd($errors);
            redirect()->intended(self::$resource::getUrl('import', ['importable' => $this->file]));

        } else {
            Notification::make()->title(__('Saved.'))->success()->send();
        }
        redirect()->intended(app($this->file->importable_resource)::getUrl());
    }

    public function headersSchemaBuilder(Importable $file): array
    {
        $header     = $this->file->header ?? [];
        $data       = $this->file->data[0] ?? [];
        $model      = app($file->importable_type);
        $attributes = $this->exclude($model->getFillable());
        $columns    = $model->getConnection()->getSchemaBuilder()->getColumns($model->getTable());
        // $foreignKeys = $model->getConnection()->getSchemaBuilder()->getForeignKeys($model->getTable());

        $this->rules = Arr::pluck(Arr::where($columns, fn($value) => $value['nullable'] == false), 'name');

        $form = Arr::flatten(
            Arr::map($attributes, function ($attribute, $key) use ($header) {
                $element = Forms\Components\Select::make($attribute)->inlineLabel()->options($header)->default($key);
                $element = $this->withValidation($attribute, $element);
                return Arr::prepend([], $element);
            })
        );

        $placeholder = Arr::flatten(
            Arr::map($data, function ($record, $key) {
                $element = Forms\Components\Placeholder::make($key)->label($key)->content($record)->inlineLabel();
                return Arr::prepend([], $element);
            })
        );

        return [
            Forms\Components\Section::make(__('Corresponding keys'))
                ->label(__('Keys marked with an asterisk must be set. Keys that are not initialized will not be stored in the database.'))
                ->schema([
                    Forms\Components\Split::make([
                        Forms\Components\Group::make()->schema($form),
                        Forms\Components\Group::make()->schema($placeholder),
                    ])->columnSpanFull()
                ])
        ];
    }

    // replace uploaded file headers with parsed headers in database
    public function parseData($data, $header, $default = null): array
    {
        $parsedDataTemp = [];
        $parsedData     = [];
        foreach ($data as $item) {
            foreach (array_keys($item) as $itemKey) {
                if ($keyExists = array_search($itemKey, $header ?? [])) {
                    $parsedDataTemp[$keyExists] = $item[$itemKey];
                }
                unset($item[$itemKey]);
            }
            $parsedData[] = $parsedDataTemp;
        }

        // replace default values
        if (array_filter(Arr::flatten($default))) {
            $parsedDataTemp = [];
            foreach ($default as $value) {
                foreach ($parsedData as $item) {
                    if (array_key_exists($value['key'], $item)) {
                        $item[$value['key']] = $value['value'];
                        $parsedDataTemp[]    = $item;
                    }
                }
            }
            $parsedData = $parsedDataTemp;
        }

        return $parsedData;
    }

    // check rules array attribute to detect fillables rules then chain it to the element
    public function withValidation($attribute, mixed $element): mixed
    {
        if (in_array($attribute, $this->rules)) {
            $element = $element
                ->hint(__('required'))
                ->hintColor('danger')
                ->hintIcon('heroicon-o-exclamation-circle')
                ->required();
        }
        return $element;
    }

    // validation rules
    public function withRules($rules): static
    {
        $this->rules = $rules;
        return $this;
    }

    // attributes to exclude
    public function withExcepts($excepts): static
    {
        $this->excepts = $excepts;
        return $this;
    }

    // only attributes
    public function onlyAttributes(array $array, array $keys): array
    {
        $only = [];
        foreach (array_keys($array) as $itemKey) {
            $keyExists = array_search($itemKey, $keys);
            if ($keyExists) {
                $only[$itemKey] = $array[$itemKey];
            }
            unset($array[$itemKey]);
        }

        return $only;
    }

    // filter and group multidimensional array values with unique keys
    public function filterByKeys(array $array): array
    {
        $output = [];
        foreach ($array as $data) {
            foreach (array_keys($data) as $key) {
                $output[$key] = array_column($array, $key);
            }
        }

        return $output;
    }

    // check a key existence in multidimensional array
    public function multiKeyExists(array $array, $key): bool
    {
        // is in base array?
        if (array_key_exists($key, $array)) {
            return true;
        }

        // check arrays contained in this array
        foreach ($array as $element) {
            if (! is_array($element)) {
                return false;
            }
            if (! $this->multiKeyExists($element, $key)) {
                return false;
            }
            return true;
        }
        return false;
    }

    // remove attributes from except array from the fillables
    public function exclude($fillables): array
    {
        $except = Arr::flatten(
            Arr::map($this->excepts, function ($attribute) use ($fillables) {
                if (in_array($attribute, $fillables)) {
                    return Arr::prepend([], $attribute);
                }
            })
        );

        foreach (array_filter($except) as $item) {
            $fillables = Arr::except($fillables, array_search($item, $fillables));
        }

        return $fillables;
    }
}
