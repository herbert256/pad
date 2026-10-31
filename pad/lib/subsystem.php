<?php

  // The subsystems whose kinds are the files of a directory - the charts, the games, the toys
  // ... - each a tag type of its own, handled by pad/types/<type>.php: written <type>:<kind>,
  // {chart:pie/}, and - when the subsystem allows it - bare, {pie/}, after every other kind of
  // tag (padTypeCommon, lib/type.php), so a tag, a store, a field or a sequence of the name
  // wins. The kinds of a subsystem are the names of the files of pad/<directory>/types/, their
  // extensions left off: a kind is added by adding its file.
  //
  // The list says, per type: its directory under pad/, whether its kinds are reached bare -
  // the icons and the fakes are not: their names, home or email, are the names of fields and
  // pages - and what a miss of the type: prefix is called (level/no.php). The bare names are
  // tried in its order, so where two subsystems have a kind of one name the first has it.

  const padSubsystems = [
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
  ];

  // The kinds of the subsystem in pad/<directory>/: the names of the files of its types/,
  // without their extensions, sorted, each once - read from the listing kept between
  // requests (padDirList, lib/dirs.php) as written on disk, so on a file system that ignores
  // case {Pie} is no kind, as a tag in another case is none (padTagBuiltIn). A directory that
  // is not there has no kinds.

  function padKinds ( $directory ) {

    static $kinds = [];

    if ( ! isset ( $kinds [$directory] ) ) {
      $names = [];
      foreach ( ( is_dir ( PAD . "$directory/types" ) ? padDirList ( PAD . "$directory/types" ) : [] ) ?: [] as $file )
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

    return isset ( padSubsystems [$type] ) and padKind ( padSubsystems [$type] [0], $name );

  }

?>
