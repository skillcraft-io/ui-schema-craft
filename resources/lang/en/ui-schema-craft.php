<?php

return [
    'name' => 'UI Schema Craft',
    'validation' => [
        'schema_not_found' => 'Schema ":identifier" not found.',
        'invalid_schema_class' => 'Invalid schema class ":class". Must extend UIComponentSchema.',
    ],
    'debug' => [
        'registered_schemas' => 'Registered UI Schemas',
        'no_schemas' => 'No schemas registered.',
    ],
    'components' => [
        'button' => [
            'submit' => 'Submit',
            'cancel' => 'Cancel',
            'save' => 'Save Changes',
        ],
        'input' => [
            'required' => 'This field is required',
            'invalid_email' => 'Please enter a valid email address',
            'min_length' => 'Must be at least :min characters',
            'max_length' => 'Must be no more than :max characters',
        ],
        'modal' => [
            'close' => 'Close',
        ],
        'alert' => [
            'success' => 'Success',
            'error' => 'Error',
            'warning' => 'Warning',
            'info' => 'Information',
        ],
    ],
];
