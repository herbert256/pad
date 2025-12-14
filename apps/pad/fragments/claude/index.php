<?php

  $title = 'PAD Tags Examples';

  // Sample data for examples
  $tags = [
    // Control Flow
    [ 'name' => 'if',        'category' => 'Control Flow',  'description' => 'Conditional execution' ],
    [ 'name' => 'case',      'category' => 'Control Flow',  'description' => 'Switch-case matching' ],
    [ 'name' => 'switch',    'category' => 'Control Flow',  'description' => 'Rotating value switch' ],
    [ 'name' => 'while',     'category' => 'Control Flow',  'description' => 'Loop while condition is true' ],
    [ 'name' => 'until',     'category' => 'Control Flow',  'description' => 'Loop until condition is true' ],

    // Variables
    [ 'name' => 'set',       'category' => 'Variables',     'description' => 'Set global variables' ],
    [ 'name' => 'get',       'category' => 'Variables',     'description' => 'Get stored content' ],
    [ 'name' => 'data',      'category' => 'Variables',     'description' => 'Store and iterate data' ],
    [ 'name' => 'content',   'category' => 'Variables',     'description' => 'Store content' ],
    [ 'name' => 'bool',      'category' => 'Variables',     'description' => 'Store boolean value' ],

    // Counters
    [ 'name' => 'count',     'category' => 'Counters',      'description' => 'Check element count' ],
    [ 'name' => 'increment', 'category' => 'Counters',      'description' => 'Increment a variable' ],
    [ 'name' => 'decrement', 'category' => 'Counters',      'description' => 'Decrement a variable' ],

    // Output
    [ 'name' => 'echo',      'category' => 'Output',        'description' => 'Evaluate and output expression' ],
    [ 'name' => 'tidy',      'category' => 'Output',        'description' => 'Format HTML content' ],
    [ 'name' => 'ignore',    'category' => 'Output',        'description' => 'Escape PAD syntax' ],
    [ 'name' => 'open',      'category' => 'Output',        'description' => 'Opening brace character' ],
    [ 'name' => 'close',     'category' => 'Output',        'description' => 'Closing brace character' ],

    // Boolean Values
    [ 'name' => 'true',      'category' => 'Boolean Values', 'description' => 'Return TRUE' ],
    [ 'name' => 'false',     'category' => 'Boolean Values', 'description' => 'Return FALSE' ],
    [ 'name' => 'null',      'category' => 'Boolean Values', 'description' => 'Return NULL' ],

    // Files
    [ 'name' => 'files',     'category' => 'Files',         'description' => 'List files in directory' ],
    [ 'name' => 'dir',       'category' => 'Files',         'description' => 'Simple directory listing' ],
    [ 'name' => 'file',      'category' => 'Files',         'description' => 'Write content to file' ],
    [ 'name' => 'exists',    'category' => 'Files',         'description' => 'Check if file exists' ],

    // Execution
    [ 'name' => 'page',      'category' => 'Execution',     'description' => 'Include PAD page' ],
    [ 'name' => 'code',      'category' => 'Execution',     'description' => 'Execute PHP code' ],
    [ 'name' => 'sandbox',   'category' => 'Execution',     'description' => 'Sandboxed PHP execution' ],
    [ 'name' => 'pad',       'category' => 'Execution',     'description' => 'PAD include tag' ],

    // Sequences
    [ 'name' => 'sequence',  'category' => 'Sequences',     'description' => 'Generate sequences' ],
    [ 'name' => 'pull',      'category' => 'Sequences',     'description' => 'Pull stored sequence' ],
    [ 'name' => 'at',        'category' => 'Sequences',     'description' => 'Access sequence by index' ],

    // Network
    [ 'name' => 'curl',      'category' => 'Network',       'description' => 'HTTP request' ],

    // Navigation
    [ 'name' => 'redirect',  'category' => 'Navigation',    'description' => 'HTTP redirect' ],
    [ 'name' => 'restart',   'category' => 'Navigation',    'description' => 'Restart PAD processing' ],

    // Debug
    [ 'name' => 'dump',      'category' => 'Debug',         'description' => 'Dump debug information' ],
    [ 'name' => 'trace',     'category' => 'Debug',         'description' => 'Enable tracing' ],

    // Errors
    [ 'name' => 'error',     'category' => 'Errors',        'description' => 'Trigger PAD error' ],
    [ 'name' => 'exception', 'category' => 'Errors',        'description' => 'Throw PHP exception' ],
    [ 'name' => 'exit',      'category' => 'Errors',        'description' => 'Exit PAD processing' ],

    // Database
    [ 'name' => 'field',     'category' => 'Database',      'description' => 'Query database field' ],
    [ 'name' => 'array',     'category' => 'Database',      'description' => 'Query database array' ],
    [ 'name' => 'record',    'category' => 'Database',      'description' => 'Execute SQL query' ],

    // Testing
    [ 'name' => 'foo',       'category' => 'Testing',       'description' => 'Test tag' ],
  ];

?>
