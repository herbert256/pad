<?php

  $title = 'PAD Reference';

  // Tags - flat list with categories
  $tags = [
    // Control Flow
    ['name' => 'if',        'desc' => 'Conditional branching',        'cat' => 'Control Flow'],
    ['name' => 'switch',    'desc' => 'Multi-way branching',          'cat' => 'Control Flow'],
    ['name' => 'case',      'desc' => 'Switch case matching',         'cat' => 'Control Flow'],
    ['name' => 'while',     'desc' => 'Loop while condition true',    'cat' => 'Control Flow'],
    ['name' => 'until',     'desc' => 'Loop until condition true',    'cat' => 'Control Flow'],
    // Variables & Data
    ['name' => 'data',      'desc' => 'Define variables',             'cat' => 'Variables'],
    ['name' => 'set',       'desc' => 'Set variable values',          'cat' => 'Variables'],
    ['name' => 'get',       'desc' => 'Include PAD pages',            'cat' => 'Variables'],
    ['name' => 'field',     'desc' => 'Access record fields',         'cat' => 'Variables'],
    ['name' => 'array',     'desc' => 'Work with arrays',             'cat' => 'Variables'],
    ['name' => 'record',    'desc' => 'Work with records',            'cat' => 'Variables'],
    ['name' => 'null',      'desc' => 'Null value handling',          'cat' => 'Variables'],
    ['name' => 'bool',      'desc' => 'Boolean values',               'cat' => 'Variables'],
    ['name' => 'true',      'desc' => 'True constant',                'cat' => 'Variables'],
    ['name' => 'false',     'desc' => 'False constant',               'cat' => 'Variables'],
    // Output
    ['name' => 'echo',      'desc' => 'Output expressions',           'cat' => 'Output'],
    ['name' => 'content',   'desc' => 'Capture content blocks',       'cat' => 'Output'],
    ['name' => 'code',      'desc' => 'Display code blocks',          'cat' => 'Output'],
    ['name' => 'dump',      'desc' => 'Debug variable dump',          'cat' => 'Output'],
    ['name' => 'trace',     'desc' => 'Execution trace',              'cat' => 'Output'],
    ['name' => 'output',    'desc' => 'Set output type',              'cat' => 'Output'],
    ['name' => 'tidy',      'desc' => 'Clean HTML output',            'cat' => 'Output'],
    // Counters
    ['name' => 'count',     'desc' => 'Count items',                  'cat' => 'Counters'],
    ['name' => 'increment', 'desc' => 'Increment counter',            'cat' => 'Counters'],
    ['name' => 'decrement', 'desc' => 'Decrement counter',            'cat' => 'Counters'],
    // Files
    ['name' => 'file',      'desc' => 'Read file contents',           'cat' => 'Files'],
    ['name' => 'files',     'desc' => 'List directory files',         'cat' => 'Files'],
    ['name' => 'dir',       'desc' => 'Simple directory listing',     'cat' => 'Files'],
    ['name' => 'exists',    'desc' => 'Check file existence',         'cat' => 'Files'],
    // Sequences
    ['name' => 'sequence',  'desc' => 'Generate sequences',           'cat' => 'Sequences'],
    ['name' => 'continue',  'desc' => 'Transform sequences',          'cat' => 'Sequences'],
    ['name' => 'pull',      'desc' => 'Retrieve stored sequences',    'cat' => 'Sequences'],
    ['name' => 'keep',      'desc' => 'Filter sequence values',       'cat' => 'Sequences'],
    ['name' => 'remove',    'desc' => 'Remove from sequences',        'cat' => 'Sequences'],
    ['name' => 'make',      'desc' => 'Transform sequence values',    'cat' => 'Sequences'],
    ['name' => 'flag',      'desc' => 'Mark sequence entries',        'cat' => 'Sequences'],
    // Database
    ['name' => 'check',     'desc' => 'Check record existence',       'cat' => 'Database'],
    // Flow
    ['name' => 'exit',      'desc' => 'Exit template execution',      'cat' => 'Flow'],
    ['name' => 'restart',   'desc' => 'Restart page execution',       'cat' => 'Flow'],
    ['name' => 'redirect',  'desc' => 'Redirect to URL',              'cat' => 'Flow'],
    ['name' => 'ignore',    'desc' => 'Skip content block',           'cat' => 'Flow'],
    // Helpers
    ['name' => 'open',      'desc' => 'Output literal {',             'cat' => 'Helpers'],
    ['name' => 'close',     'desc' => 'Output literal }',             'cat' => 'Helpers'],
    ['name' => 'pad',       'desc' => 'PAD metadata',                 'cat' => 'Helpers'],
    ['name' => 'page',      'desc' => 'Page information',             'cat' => 'Helpers'],
    ['name' => 'subpage',   'desc' => 'Subpage handling',             'cat' => 'Helpers'],
    ['name' => 'sandbox',   'desc' => 'Isolated execution',           'cat' => 'Helpers'],
    ['name' => 'at',        'desc' => 'Iteration position tags',      'cat' => 'Helpers'],
    // Errors
    ['name' => 'error',     'desc' => 'Error handling',               'cat' => 'Errors'],
    ['name' => 'exception', 'desc' => 'Exception handling',           'cat' => 'Errors'],
    // External
    ['name' => 'curl',      'desc' => 'HTTP requests',                'cat' => 'External'],
    ['name' => 'ajax',      'desc' => 'AJAX responses',               'cat' => 'External'],
    ['name' => 'action',    'desc' => 'Form actions',                 'cat' => 'External'],
  ];

  // Properties
  $props = [
    // Iteration
    ['name' => 'first',     'desc' => 'Is first iteration',           'cat' => 'Iteration'],
    ['name' => 'last',      'desc' => 'Is last iteration',            'cat' => 'Iteration'],
    ['name' => 'notFirst',  'desc' => 'Not first iteration',          'cat' => 'Iteration'],
    ['name' => 'notLast',   'desc' => 'Not last iteration',           'cat' => 'Iteration'],
    ['name' => 'border',    'desc' => 'Is first or last',             'cat' => 'Iteration'],
    ['name' => 'middle',    'desc' => 'Neither first nor last',       'cat' => 'Iteration'],
    ['name' => 'even',      'desc' => 'Even occurrence',              'cat' => 'Iteration'],
    ['name' => 'odd',       'desc' => 'Odd occurrence',               'cat' => 'Iteration'],
    // Counters
    ['name' => 'current',   'desc' => 'Current position (1-based)',   'cat' => 'Counters'],
    ['name' => 'count',     'desc' => 'Total item count',             'cat' => 'Counters'],
    ['name' => 'remaining', 'desc' => 'Items remaining',              'cat' => 'Counters'],
    ['name' => 'done',      'desc' => 'Items completed',              'cat' => 'Counters'],
    // Data
    ['name' => 'data',      'desc' => 'Full data array',              'cat' => 'Data'],
    ['name' => 'key',       'desc' => 'Current array key',            'cat' => 'Data'],
    ['name' => 'keys',      'desc' => 'All keys (iterable)',          'cat' => 'Data'],
    ['name' => 'fields',    'desc' => 'Field name/value pairs',       'cat' => 'Data'],
    ['name' => 'firstFieldName',  'desc' => 'First field name',       'cat' => 'Data'],
    ['name' => 'firstFieldValue', 'desc' => 'First field value',      'cat' => 'Data'],
    // Metadata
    ['name' => 'name',      'desc' => 'Current tag name',             'cat' => 'Metadata'],
    ['name' => 'parameter', 'desc' => 'Named parameter value',        'cat' => 'Metadata'],
    ['name' => 'parameters','desc' => 'All parameters',               'cat' => 'Metadata'],
    ['name' => 'option',    'desc' => 'Positional option',            'cat' => 'Metadata'],
    ['name' => 'options',   'desc' => 'All options',                  'cat' => 'Metadata'],
    ['name' => 'variable',  'desc' => 'Level variable',               'cat' => 'Metadata'],
    ['name' => 'variables', 'desc' => 'All level variables',          'cat' => 'Metadata'],
  ];

  // Functions
  $funcs = [
    // Extraction
    ['name' => 'after',     'desc' => 'After first delimiter',        'cat' => 'Extraction'],
    ['name' => 'afterLast', 'desc' => 'After last delimiter',         'cat' => 'Extraction'],
    ['name' => 'before',    'desc' => 'Before first delimiter',       'cat' => 'Extraction'],
    ['name' => 'beforeLast','desc' => 'Before last delimiter',        'cat' => 'Extraction'],
    // Substring
    ['name' => 'substr',    'desc' => 'Extract by position',          'cat' => 'Substring'],
    ['name' => 'left',      'desc' => 'First N characters',           'cat' => 'Substring'],
    ['name' => 'right',     'desc' => 'Last N characters',            'cat' => 'Substring'],
    ['name' => 'mid',       'desc' => 'Middle substring',             'cat' => 'Substring'],
    // Case
    ['name' => 'upper',     'desc' => 'To uppercase',                 'cat' => 'Case'],
    ['name' => 'lower',     'desc' => 'To lowercase',                 'cat' => 'Case'],
    ['name' => 'capitalize','desc' => 'Title case',                   'cat' => 'Case'],
    ['name' => 'ucwords',   'desc' => 'Title case (alias)',           'cat' => 'Case'],
    // Manipulation
    ['name' => 'trim',      'desc' => 'Remove whitespace',            'cat' => 'Manipulation'],
    ['name' => 'replace',   'desc' => 'Replace text',                 'cat' => 'Manipulation'],
    ['name' => 'cut',       'desc' => 'Remove text',                  'cat' => 'Manipulation'],
    ['name' => 'white',     'desc' => 'Normalize spaces',             'cat' => 'Manipulation'],
    // Encoding
    ['name' => 'html',      'desc' => 'HTML escape',                  'cat' => 'Encoding'],
    ['name' => 'sanitize',  'desc' => 'Full sanitization',            'cat' => 'Encoding'],
    ['name' => 'url',       'desc' => 'URL encode',                   'cat' => 'Encoding'],
    ['name' => 'slashes',   'desc' => 'Add slashes',                  'cat' => 'Encoding'],
    ['name' => 'stripslashes', 'desc' => 'Remove slashes',            'cat' => 'Encoding'],
    ['name' => 'encodeHigh','desc' => 'Encode high ASCII',            'cat' => 'Encoding'],
    ['name' => 'stripLow',  'desc' => 'Strip control chars',          'cat' => 'Encoding'],
    // HTML
    ['name' => 'bold',      'desc' => 'Wrap in <b> tags',             'cat' => 'HTML'],
    ['name' => 'nbsp',      'desc' => 'Non-breaking spaces',          'cat' => 'HTML'],
    ['name' => 'max_len',   'desc' => 'Truncate to length',           'cat' => 'HTML'],
    // Testing
    ['name' => 'contains',  'desc' => 'Contains substring',           'cat' => 'Testing'],
    ['name' => 'in',        'desc' => 'Value in list',                'cat' => 'Testing'],
    ['name' => 'like',      'desc' => 'Pattern matching',             'cat' => 'Testing'],
    ['name' => 'between',   'desc' => 'Exclusive range',              'cat' => 'Testing'],
    ['name' => 'range',     'desc' => 'Inclusive range',              'cat' => 'Testing'],
    ['name' => 'exists',    'desc' => 'File exists',                  'cat' => 'Testing'],
    // Date
    ['name' => 'now',       'desc' => 'Current timestamp',            'cat' => 'Date/Time'],
    ['name' => 'date',      'desc' => 'Format date',                  'cat' => 'Date/Time'],
    ['name' => 'time',      'desc' => 'Format time',                  'cat' => 'Date/Time'],
    ['name' => 'timestamp', 'desc' => 'Format timestamp',             'cat' => 'Date/Time'],
    // Helpers
    ['name' => 'open',      'desc' => 'Literal open brace',           'cat' => 'Helpers'],
    ['name' => 'close',     'desc' => 'Literal close brace',          'cat' => 'Helpers'],
    ['name' => 'tag',       'desc' => 'Create tag syntax',            'cat' => 'Helpers'],
    ['name' => 'optional',  'desc' => 'Null coalescing',              'cat' => 'Helpers'],
  ];

?>
