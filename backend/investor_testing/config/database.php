<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'investor_testing'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'investor_testing' => [
            'charset' => env('INVESTOR_TESTING_CHARSET', 'utf8'),
            'database' => env('INVESTOR_TESTING_DATABASE', 'investor_testing'),
            'driver' => 'pgsql',
            'host' => env('INVESTOR_TESTING_HOST', 'localhost'),
            'password' => env('INVESTOR_TESTING_PASSWORD', 'investor_testing'),
            'port' => env('INVESTOR_TESTING_PORT', '5432'),
            'prefix' => '',
            'schema' => env('INVESTOR_TESTING_SCHEMA', 'public'),
            'timezone' => env('INVESTOR_TESTING_TIMEZONE', config('app.timezone')),
            'username' => env('INVESTOR_TESTING_USERNAME', 'investor_testing'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',
];
