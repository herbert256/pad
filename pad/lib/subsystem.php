<?php

  // Registered subsystem namespaces. Each kind is a file of its types/ directory.
  // Named template tags always require the prefix, for example {chart:pie/}.
  // The middle registry field retains expression-lookup compatibility; it does not
  // authorize unprefixed template tags. The final field names a missing kind in errors.

  // Sequence namespaces with an explicit auto selector for parameter-driven runs.
  const padSequenceOperations = [ 'sequence', 'action', 'keep', 'make', 'remove' ];

  // Virtual selectors preserve parameter-driven selection without pretending to be
  // a provider, language or file format in the underlying directory registry.
  const padSubsystemAuto = [ 'map', 'holidays', 'search', 'ai', 'format' ];

  const padSubsystems = [
    'diff' => [ 'diffs', FALSE, 'diff type' ],
    'table' => [ 'tables', FALSE, 'table type' ],
    'time' => [ 'times', FALSE, 'time display' ],
    'timeline' => [ 'timelines', FALSE, 'timeline type' ],

    'image' => [ 'images', FALSE, 'image type' ],
    'avatar' => [ 'avatars', FALSE, 'avatar type' ],

    'map'      => [ 'maps', FALSE, 'map view' ],
    'holidays' => [ 'holidays', FALSE, 'holiday country' ],
    'search'   => [ 'search', FALSE, 'search language' ],
    'ai'       => [ 'ai', FALSE, 'AI provider' ],
    'format'   => [ 'formats', FALSE, 'data format' ],
    'sheet'    => [ 'sheets', FALSE, 'sheet view' ],
    'calendar' => [ 'calendars', FALSE, 'calendar type' ],
    'chart'   => [ 'charts',   TRUE,  'chart type'     ],
    'game'    => [ 'games',    TRUE,  'game'           ],
    'toy'     => [ 'toys',     TRUE,  'toy'            ],
    'diagram' => [ 'diagrams', TRUE,  'diagram type'   ],
    'widget'  => [ 'widgets',  TRUE,  'widget'         ],
    'barcode' => [ 'codes',    TRUE,  'barcode type'   ],
    'embed'   => [ 'embeds',   TRUE,  'embed provider' ],
    'input'   => [ 'inputs',   TRUE,  'input'          ],
    'icon'    => [ 'icons',    FALSE, 'icon'           ],
    'fake'    => [ 'fakes',    FALSE, 'fake kind'      ],
    'pattern' => [ 'patterns', FALSE, 'pattern'        ],
    'sound'   => [ 'sounds',   FALSE, 'sound'          ],
    'printable' => [ 'printables', FALSE, 'printable'  ],
    'notation' => [ 'notations', TRUE, 'notation'      ],
    'section' => [ 'sections', FALSE, 'section'        ],
    'effect'  => [ 'effects',  TRUE,  'effect'         ],
    'jsonld'  => [ 'jsonld',   FALSE, 'JSON-LD kind'   ],
  ];

  // The subsystems of pad/subs/ whose kinds are no tag type - a view of {map:auto}, a rule of
  // padValidate, a provider of padOAuthStart, a format of {format:auto}, a function of {sheet:grid} ...
  // - by the name the use of a kind is recorded under (padInfoXref, which files every kind of a
  // subsystem whatever the page's text says) and their directory.

  const padSubsystemKinds = [
    'map'      => 'maps',
    'rule'     => 'rules',
    'oauth'    => 'oauth',
    'channel'  => 'channels',
    'ai'       => 'ai',
    'filter'   => 'filters',
    'holidays' => 'holidays',
    'search'   => 'search',
    'format'   => 'formats',
    'formula'  => 'formulas',
    'theme'    => 'themes',
    'palette'  => 'palettes',
    'highlighter' => 'highlighters',
    'diff' => 'diffs',
    'recurrence' => 'recurrences',
    'geo' => 'geospatial',
    'distribution' => 'distributions',
    'unit' => 'units',
  ];

  // The kinds of the subsystem in pad/subs/<directory>/: the names of the files of its types/,
  // without their extensions, sorted, each once - read from the listing kept between
  // requests (padDirList, lib/dirs.php) as written on disk, so on a file system that ignores
  // case {Pie} is no kind, as a tag in another case is none (padTagBuiltIn). A directory that
  // is not there has no kinds.

  function padKinds ( $directory ) {

    static $kinds = [];

    if ( ! isset ( $kinds [$directory] ) ) {
      $names = [];
      foreach ( ( is_dir ( PAD . "subs/$directory/types" ) ? padDirList ( PAD . "subs/$directory/types" ) : [] ) ?: [] as $file )
        if ( $file [0] != '.' and ( $dot = strrpos ( $file, '.' ) ) )
          $names [ substr ( $file, 0, $dot ) ] = TRUE;
      $names = array_keys ( $names );
      sort ( $names );
      $kinds [$directory] = $names;
    }

    return $kinds [$directory];

  }

  function padKind ( $directory, $name ) {

    static $sets = [];

    $sets [$directory] ??= array_flip ( padKinds ( $directory ) );

    return is_string ( $name ) and isset ( $sets [$directory] [$name] );

  }

  // Names available as tags, including parameter-driven selectors. padKinds remains
  // the actual file registry used by provider validation and format detection.

  function padSubsystemTagKinds ( $type ) {

    if ( ! isset ( padSubsystems [$type] ) ) return [];
    $kinds = padKinds ( padSubsystems [$type] [0] );
    if ( in_array ( $type, padSubsystemAuto, TRUE ) ) $kinds [] = 'auto';
    sort ( $kinds );
    return $kinds;

  }

  // The type a bare name is a kind of - the first subsystem of the list that reaches its kinds
  // bare and has one of the name - or FALSE.

  function padSubsystemBare ( $name ) {

    foreach ( padSubsystems as $type => [ $directory, $bare ] )
      if ( $bare and padKind ( $directory, $name ) )
        return $type;

    return FALSE;

  }

  // Whether the subsystem of a type: prefix has a kind of the name.

  function padSubsystemHas ( $type, $name ) {

    return isset ( padSubsystems [$type] ) and ( padKind ( padSubsystems [$type] [0], $name )
      or ( $name === 'auto' and in_array ( $type, padSubsystemAuto, TRUE ) ) );

  }

?>
