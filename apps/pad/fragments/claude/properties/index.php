<?php

  $title = 'Tag Properties';

  // List of all tag properties with descriptions
  $properties = [
    // Iteration State
    ['name' => 'first',           'description' => 'TRUE if first iteration',           'category' => 'Iteration State'],
    ['name' => 'last',            'description' => 'TRUE if last iteration',            'category' => 'Iteration State'],
    ['name' => 'notFirst',        'description' => 'TRUE if NOT first iteration',       'category' => 'Iteration State'],
    ['name' => 'notLast',         'description' => 'TRUE if NOT last iteration',        'category' => 'Iteration State'],
    ['name' => 'border',          'description' => 'TRUE if first OR last',             'category' => 'Iteration State'],
    ['name' => 'middle',          'description' => 'TRUE if neither first nor last',    'category' => 'Iteration State'],
    ['name' => 'even',            'description' => 'TRUE if even occurrence',           'category' => 'Iteration State'],
    ['name' => 'odd',             'description' => 'TRUE if odd occurrence',            'category' => 'Iteration State'],

    // Counters
    ['name' => 'current',         'description' => 'Current occurrence number (1-based)', 'category' => 'Counter'],
    ['name' => 'count',           'description' => 'Total number of items',              'category' => 'Counter'],
    ['name' => 'remaining',       'description' => 'Items remaining after current',      'category' => 'Counter'],
    ['name' => 'done',            'description' => 'Items completed before current',     'category' => 'Counter'],

    // Data Access
    ['name' => 'data',            'description' => 'Complete data array',                'category' => 'Data Access'],
    ['name' => 'key',             'description' => 'Current array key',                  'category' => 'Data Access'],
    ['name' => 'keys',            'description' => 'All keys with values (iterable)',    'category' => 'Data Access'],
    ['name' => 'fields',          'description' => 'Field name/value pairs (iterable)',  'category' => 'Data Access'],
    ['name' => 'firstFieldName',  'description' => 'Name of first field',                'category' => 'Data Access'],
    ['name' => 'firstFieldValue', 'description' => 'Value of first field',               'category' => 'Data Access'],

    // Tag Metadata
    ['name' => 'name',            'description' => 'Name of current tag',                'category' => 'Tag Metadata'],

    // Parameters & Options
    ['name' => 'parameter',       'description' => 'Named parameter value',              'category' => 'Parameters'],
    ['name' => 'parameters',      'description' => 'All named parameters (iterable)',    'category' => 'Parameters'],
    ['name' => 'option',          'description' => 'Positional option value',            'category' => 'Parameters'],
    ['name' => 'options',         'description' => 'All positional options (iterable)',  'category' => 'Parameters'],

    // Variables
    ['name' => 'variable',        'description' => 'Level-scoped variable value',        'category' => 'Variables'],
    ['name' => 'variables',       'description' => 'All level variables (iterable)',     'category' => 'Variables'],
  ];

?>
