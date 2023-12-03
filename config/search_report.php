<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Search Term Logging Configuration
    |--------------------------------------------------------------------------
    |
    | The 'Store Searches' setting determines if search terms are logged in
    | the database. When set to true, the search term logger captures and
    | stores the results, allowing for data analysis and optimization of
    | search to help locate potential content gaps and opportunities.
    |
    */

    'store_searches' => true,

    /*
    |--------------------------------------------------------------------------
    | Authenticated User Search Logging Configuration
    |--------------------------------------------------------------------------
    |
    | The 'Ignore Authenticated Users' option configures the logging of search
    | queries made by signed-in users. When enabled, it prevents the storage
    | of search activities from authenticated accounts. This can be useful
    | when the only authenticated users are administrators or editors.
    |
    */

    'ignore_authenticated_users' => true,

    /*
    |--------------------------------------------------------------------------
    | Search Logs Database Configuration
    |--------------------------------------------------------------------------
    |
    | The 'Database' configuration key lets you customize the search term logs'
    | table name and database connection, if you'd prefer to store the data
    | in a different database or table. The default connection is MySQL.
    |
    */

    'database' => [
        'connection' => config('database.default', 'mysql'),
        'table' => 'search_term_logs',
    ],

];
