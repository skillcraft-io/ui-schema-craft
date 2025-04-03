<?php

return [
    'schemas' => [],
    'enable_examples' => env('UI_SCHEMA_CRAFT_ENABLE_EXAMPLES', false),
    'dd_examples' => env('UI_SCHEMA_CRAFT_DD_EXAMPLES', false),
    /*
    |--------------------------------------------------------------------------
    | Default Schema Settings
    |--------------------------------------------------------------------------
    |
    | Here you can specify default settings for your UI components.
    |
    */

    'defaults' => [
        'theme' => 'default',
        'size' => 'md',
    ],
];
