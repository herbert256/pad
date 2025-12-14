<?php

  $title = 'Pipe Functions';

  // List of all pipe functions with descriptions
  $functions = [
    // String Extraction
    ['name' => 'after',       'description' => 'Everything after first delimiter',      'category' => 'String Extraction'],
    ['name' => 'afterLast',   'description' => 'Everything after last delimiter',       'category' => 'String Extraction'],
    ['name' => 'before',      'description' => 'Everything before first delimiter',     'category' => 'String Extraction'],
    ['name' => 'beforeLast',  'description' => 'Everything before last delimiter',      'category' => 'String Extraction'],

    // Substring
    ['name' => 'substr',      'description' => 'Extract substring by position',         'category' => 'Substring'],
    ['name' => 'left',        'description' => 'First N characters',                    'category' => 'Substring'],
    ['name' => 'right',       'description' => 'Last N characters',                     'category' => 'Substring'],
    ['name' => 'mid',         'description' => 'Substring from position (1-based)',     'category' => 'Substring'],

    // Case Conversion
    ['name' => 'upper',       'description' => 'Convert to uppercase',                  'category' => 'Case'],
    ['name' => 'lower',       'description' => 'Convert to lowercase',                  'category' => 'Case'],
    ['name' => 'capitalize',  'description' => 'Capitalize first letter of each word', 'category' => 'Case'],
    ['name' => 'ucwords',     'description' => 'Alias for capitalize',                  'category' => 'Case'],

    // String Manipulation
    ['name' => 'trim',        'description' => 'Remove whitespace from ends',           'category' => 'Manipulation'],
    ['name' => 'replace',     'description' => 'Replace occurrences of text',           'category' => 'Manipulation'],
    ['name' => 'cut',         'description' => 'Remove all occurrences of text',        'category' => 'Manipulation'],
    ['name' => 'white',       'description' => 'Normalize whitespace',                  'category' => 'Manipulation'],

    // Encoding
    ['name' => 'html',        'description' => 'Escape HTML special characters',        'category' => 'Encoding'],
    ['name' => 'sanitize',    'description' => 'Full special character sanitization',   'category' => 'Encoding'],
    ['name' => 'url',         'description' => 'URL-encode the value',                  'category' => 'Encoding'],
    ['name' => 'slashes',     'description' => 'Add backslashes before quotes',         'category' => 'Encoding'],
    ['name' => 'stripslashes','description' => 'Remove backslashes',                    'category' => 'Encoding'],
    ['name' => 'encodeHigh',  'description' => 'Encode high ASCII characters',          'category' => 'Encoding'],
    ['name' => 'stripLow',    'description' => 'Strip low ASCII control characters',    'category' => 'Encoding'],

    // HTML Formatting
    ['name' => 'bold',        'description' => 'Wrap in <b> tags',                      'category' => 'HTML'],
    ['name' => 'nbsp',        'description' => 'Replace spaces with &nbsp;',            'category' => 'HTML'],

    // Length Control
    ['name' => 'max_len',     'description' => 'Truncate to maximum length',            'category' => 'Length'],

    // Testing
    ['name' => 'contains',    'description' => 'Test if value contains substring',      'category' => 'Testing'],
    ['name' => 'in',          'description' => 'Test if value is in list',              'category' => 'Testing'],
    ['name' => 'like',        'description' => 'SQL LIKE pattern matching',             'category' => 'Testing'],
    ['name' => 'between',     'description' => 'Test if exclusively between values',    'category' => 'Testing'],
    ['name' => 'range',       'description' => 'Test if inclusively in range',          'category' => 'Testing'],
    ['name' => 'exists',      'description' => 'Test if file exists',                   'category' => 'Testing'],

    // Date/Time
    ['name' => 'now',         'description' => 'Current Unix timestamp',                'category' => 'Date/Time'],
    ['name' => 'date',        'description' => 'Format timestamp as date',              'category' => 'Date/Time'],
    ['name' => 'time',        'description' => 'Alias for date',                        'category' => 'Date/Time'],
    ['name' => 'timestamp',   'description' => 'Alias for date',                        'category' => 'Date/Time'],

    // PAD Helpers
    ['name' => 'open',        'description' => 'Output literal opening brace',          'category' => 'PAD Helpers'],
    ['name' => 'close',       'description' => 'Output literal closing brace',          'category' => 'PAD Helpers'],
    ['name' => 'tag',         'description' => 'Wrap value as PAD tag',                 'category' => 'PAD Helpers'],
    ['name' => 'optional',    'description' => 'Return value or empty if null',         'category' => 'PAD Helpers'],
  ];

?>
