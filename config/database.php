<?php

use Illuminate\Support\Str;

return [

    // Default connection for general use
    'default' => env('DB_CONNECTION', 'sqlsrv'),

    'connections' => [

        // TestDB connection
        'testdb' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'testdb'),
            'username' => env('DB_USERNAME', 'dadoy'),
            'password' => env('DB_PASSWORD', '2021-02083'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        // ES_Obrero connection
        'sqlsrv1' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_SQLSRV1_HOST', '172.16.210.20'),
            'port' => env('DB_SQLSRV1_PORT', '1433'),
            'database' => env('DB_SQLSRV1_DATABASE', 'ES_Obrero'),
            'username' => env('DB_SQLSRV1_USERNAME', 'useptextblast'),
            'password' => env('DB_SQLSRV1_PASSWORD', 'US3Pt3xtb@st'),
            'charset' => 'utf8',
            'prefix' => '',
        ],

        // Tagum connection (example placeholder, update accordingly)
        // 'sqlsrv2' => [
        //     'driver' => 'sqlsrv',
        //     'host' => env('DB_SQLSRV2_HOST', ''),
        //     'port' => env('DB_SQLSRV2_PORT', '1433'),
        //     'database' => env('DB_SQLSRV2_DATABASE', ''),
        //     'username' => env('DB_SQLSRV2_USERNAME', ''),
        //     'password' => env('DB_SQLSRV2_PASSWORD', ''),
        //     'charset' => 'utf8',
        //     'prefix' => '',
        // ],

        // Mabini connection (example placeholder, update accordingly)
        // 'sqlsrv3' => [
        //     'driver' => 'sqlsrv',
        //     'host' => env('DB_SQLSRV3_HOST', ''),
        //     'port' => env('DB_SQLSRV3_PORT', '1433'),
        //     'database' => env('DB_SQLSRV3_DATABASE', ''),
        //     'username' => env('DB_SQLSRV3_USERNAME', ''),
        //     'password' => env('DB_SQLSRV3_PASSWORD', ''),
        //     'charset' => 'utf8',
        //     'prefix' => '',
        // ],
    ],

    'migrations' => 'migrations',
];