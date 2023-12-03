<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default search index
    |--------------------------------------------------------------------------
    |
    | This option controls the search index that gets queried when performing
    | search functions without explicitly selecting another index.
    |
    */

    'default' => env('STATAMIC_DEFAULT_SEARCH_INDEX', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Search Indexes
    |--------------------------------------------------------------------------
    |
    | Here you can define all of the available search indexes.
    |
    */

    'indexes' => [

        'default' => [
            'driver' => 'local',
            'searchables' => 'all',
            'fields' => ['title'],
        ],

        'docs' => [
            'driver' => 'local',
            'searchables' => [
                'docs:documentation',
                'docs:v0.0.1',
            ],
            'fields' => [
                'title',
                'id',
                'search_title',
                'search_url',
                'search_content',
                'origin_title',
                'origin_url',
                'is_root',
                'additional_context',
                'code_samples',
            ],
            'header_threshold' => 1,
            'skip_headers' => [
            ],
            'document_transformers' => [
                \Stillat\DocumentationSearch\Indexing\Transformers\Document\SoundexTransformer::class,
            ],
            'query_transformers' => [
                \Stillat\DocumentationSearch\Indexing\Transformers\Query\SoundexTransformer::class,
            ],
        ],

        // 'blog' => [
        //     'driver' => 'local',
        //     'searchables' => 'collection:blog',
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Driver Defaults
    |--------------------------------------------------------------------------
    |
    | Here you can specify default configuration to be applied to all indexes
    | that use the corresponding driver. For instance, if you have two
    | indexes that use the "local" driver, both of them can have the
    | same base configuration. You may override for each index.
    |
    */

    'drivers' => [

        'local' => [
            'path' => storage_path('statamic/search'),
        ],

        'algolia' => [
            'credentials' => [
                'id' => env('ALGOLIA_APP_ID', ''),
                'secret' => env('ALGOLIA_SECRET', ''),
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Search Defaults
    |--------------------------------------------------------------------------
    |
    | Here you can specify default configuration to be applied to all indexes
    | regardless of the driver. You can override these per driver or per index.
    |
    */

    'defaults' => [
        'fields' => ['title'],
    ],

];
