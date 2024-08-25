<?php

return [
    /*
     * This model will be used to import.
     */
    'import_model' => \IracodeCom\FilamentExcelPro\Model\Importable::class,

    /*
     * This model will be determined as user model.
     * creator, updater: foreign key columns in import model for creator, updater relationships
     */
    'user'         => [
        'model'   => \App\Models\User::class,
        'creator' => 'created_by',
        'updater' => 'updated_by'
    ],

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Import model shipped with this package.
     */
    'table'        => 'importables',

    'resources' => [
        'label'                  => 'Excel Import',
        'plural_label'           => 'Excel Imports',
        'navigation_group'       => null,
        'navigation_icon'        => 'heroicon-o-clipboard-document-check',
        'navigation_sort'        => null,
        'navigation_count_badge' => false,
        'resource'               => \IracodeCom\FilamentExcelPro\Resources\ImportableResource::class,
    ],

    'datetime_format' => 'd/m/Y H:i:s',
];