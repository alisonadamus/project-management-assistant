<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    'default_namespace' => 'Alison\\ProjectManagementAssistant\\Filament',

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    | These are the default namespaces where Filament looks for classes to
    | automatically discover. You may pass each namespace as a string or an array.
    |
    */
    'namespaces' => [
        'pages' => [
            'Alison\\ProjectManagementAssistant\\Filament\\Pages',
        ],
        'resources' => [
            'Alison\\ProjectManagementAssistant\\Filament\\Resources',
        ],
        'widgets' => [
            'Alison\\ProjectManagementAssistant\\Filament\\Widgets',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | These are the default paths where Filament looks for classes to
    | automatically discover. You may pass each path as a string or an array.
    |
    */
    'paths' => [
        'pages' => [
            app_path('Filament/Pages'),
        ],
        'resources' => [
            app_path('Filament/Resources'),
        ],
        'widgets' => [
            app_path('Filament/Widgets'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    |
    | This is the namespace and directory that Filament will automatically
    | register pages from. You may also register pages here.
    |
    */
    'pages' => [
        'namespace' => 'Alison\\ProjectManagementAssistant\\Filament\\Pages',
        'path' => app_path('Filament/Pages'),
        'register' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | This is the namespace and directory that Filament will automatically
    | register resources from. You may also register resources here.
    |
    */
    'resources' => [
        'namespace' => 'Alison\\ProjectManagementAssistant\\Filament\\Resources',
        'path' => app_path('Filament/Resources'),
        'register' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    |
    | This is the namespace and directory that Filament will automatically
    | register widgets from. You may also register widgets here.
    |
    */
    'widgets' => [
        'namespace' => 'Alison\\ProjectManagementAssistant\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
        'register' => [],
    ],
];
