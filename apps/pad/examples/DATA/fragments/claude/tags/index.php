<?php

  $title = 'PAD Tag Examples';

  // Tags organized by category
  $tags = [
    // Control Flow
    [ 'name' => 'if',        'category' => 'Control Flow', 'description' => 'Conditional execution' ],
    [ 'name' => 'case',      'category' => 'Control Flow', 'description' => 'Multi-branch conditional' ],
    [ 'name' => 'switch',    'category' => 'Control Flow', 'description' => 'Alternating values' ],
    [ 'name' => 'while',     'category' => 'Control Flow', 'description' => 'Loop while condition true' ],
    [ 'name' => 'until',     'category' => 'Control Flow', 'description' => 'Loop until condition true' ],

    // Variables
    [ 'name' => 'set',       'category' => 'Variables', 'description' => 'Assign variable value' ],
    [ 'name' => 'get',       'category' => 'Variables', 'description' => 'Get variable value' ],
    [ 'name' => 'data',      'category' => 'Variables', 'description' => 'Define inline data' ],
    [ 'name' => 'content',   'category' => 'Variables', 'description' => 'Define reusable content block' ],
    [ 'name' => 'bool',      'category' => 'Variables', 'description' => 'Define boolean variable' ],

    // Counters
    [ 'name' => 'count',     'category' => 'Counters', 'description' => 'Check if array has elements' ],
    [ 'name' => 'increment', 'category' => 'Counters', 'description' => 'Increment variable by 1' ],
    [ 'name' => 'decrement', 'category' => 'Counters', 'description' => 'Decrement variable by 1' ],

    // Output
    [ 'name' => 'echo',      'category' => 'Output', 'description' => 'Evaluate and output expression' ],
    [ 'name' => 'output',    'category' => 'Output', 'description' => 'Control output buffering' ],
    [ 'name' => 'tidy',      'category' => 'Output', 'description' => 'Clean up whitespace' ],
    [ 'name' => 'ignore',    'category' => 'Output', 'description' => 'Suppress output' ],
    [ 'name' => 'open',      'category' => 'Output', 'description' => 'Output literal {' ],
    [ 'name' => 'close',     'category' => 'Output', 'description' => 'Output literal }' ],

    // Boolean Values
    [ 'name' => 'true',      'category' => 'Boolean', 'description' => 'Always true condition' ],
    [ 'name' => 'false',     'category' => 'Boolean', 'description' => 'Always false condition' ],
    [ 'name' => 'null',      'category' => 'Boolean', 'description' => 'Null value' ],

    // Files
    [ 'name' => 'files',     'category' => 'Files', 'description' => 'List directory contents' ],
    [ 'name' => 'dir',       'category' => 'Files', 'description' => 'Simple directory listing' ],
    [ 'name' => 'file',      'category' => 'Files', 'description' => 'Write content to file' ],
    [ 'name' => 'exists',    'category' => 'Files', 'description' => 'Check if file exists' ],

    // Execution
    [ 'name' => 'page',      'category' => 'Execution', 'description' => 'Include another page' ],
    [ 'name' => 'code',      'category' => 'Execution', 'description' => 'Create execution scope' ],
    [ 'name' => 'sandbox',   'category' => 'Execution', 'description' => 'Isolated execution scope' ],
    [ 'name' => 'pad',       'category' => 'Execution', 'description' => 'Process PAD template' ],

    // Sequences
    [ 'name' => 'sequence',  'category' => 'Sequences', 'description' => 'Generate numeric sequence' ],
    [ 'name' => 'pull',      'category' => 'Sequences', 'description' => 'Pull value from sequence' ],
    [ 'name' => 'continue',  'category' => 'Sequences', 'description' => 'Skip to next iteration' ],
    [ 'name' => 'flag',      'category' => 'Sequences', 'description' => 'Set iteration flag' ],
    [ 'name' => 'keep',      'category' => 'Sequences', 'description' => 'Keep item in result' ],
    [ 'name' => 'remove',    'category' => 'Sequences', 'description' => 'Remove item from result' ],
    [ 'name' => 'make',      'category' => 'Sequences', 'description' => 'Create new item' ],
    [ 'name' => 'at',        'category' => 'Sequences', 'description' => 'Access item at position' ],

    // Network
    [ 'name' => 'curl',      'category' => 'Network', 'description' => 'HTTP request' ],
    [ 'name' => 'action',    'category' => 'Network', 'description' => 'Form action handler' ],
    [ 'name' => 'ajax',      'category' => 'Network', 'description' => 'AJAX request handler' ],
    [ 'name' => 'redirect',  'category' => 'Network', 'description' => 'Redirect to URL' ],
    [ 'name' => 'restart',   'category' => 'Network', 'description' => 'Restart page processing' ],

    // Debug
    [ 'name' => 'dump',      'category' => 'Debug', 'description' => 'Dump debug information' ],
    [ 'name' => 'trace',     'category' => 'Debug', 'description' => 'Output execution trace' ],
    [ 'name' => 'error',     'category' => 'Debug', 'description' => 'Trigger error' ],
    [ 'name' => 'exception', 'category' => 'Debug', 'description' => 'Throw exception' ],
    [ 'name' => 'exit',      'category' => 'Debug', 'description' => 'Stop execution' ],

    // Database
    [ 'name' => 'field',     'category' => 'Database', 'description' => 'Get single field value' ],
    [ 'name' => 'array',     'category' => 'Database', 'description' => 'Get multiple rows' ],
    [ 'name' => 'record',    'category' => 'Database', 'description' => 'Get single row' ],
    [ 'name' => 'check',     'category' => 'Database', 'description' => 'Check if record exists' ],

    // Misc
    [ 'name' => 'foo',       'category' => 'Misc', 'description' => 'Test/placeholder tag' ],
  ];

?>
