<?php

  // Writes the editor completion data from the framework source, so a tag, a pipe function,
  // an option, a property, a type prefix or a sequence type added to pad/ reaches every
  // editor kit with one run:
  //
  //   php editors/generate.php           rewrites the three files
  //   php editors/generate.php --check   writes nothing, exits 1 when a file is stale
  //
  // ci.sh runs the --check form, so a built-in that is missing from the lists fails the gate.
  //
  //   vscode/pad/completions.json        VS Code / Cursor extension
  //   lsp/completions.json               the zero-dependency language server - the same list
  //   sublime/PAD.sublime-completions    Sublime Text
  //
  // Every list is the directory it comes from, sorted: pad/tags, pad/functions, pad/properties,
  // pad/types (the type prefixes), pad/sequence/types (one directory per type), and for the
  // options pad/options, the start-phase names of inits/const.php and pad/handling/types.
  // Sublime inserts a property with its @, ready for the tag name.

  $root = dirname ( __DIR__ );
  $pad  = "$root/pad";

  function generateNames ( $dir, $dirs = FALSE ) {

    $names = [];

    foreach ( scandir ( $dir ) as $one ) {

      if ( $one [0] == '.' or $one [0] == '_' )
        continue;

      if ( $dirs and is_dir ( "$dir/$one" ) )
        $names [] = $one;
      elseif ( ! $dirs and str_ends_with ( $one, '.php' ) )
        $names [] = substr ( $one, 0, -4 );

    }

    sort ( $names, SORT_STRING | SORT_FLAG_CASE );

    return $names;

  }

  function generateStartOptions ( $file ) {

    preg_match ( "/define \( 'padOptionsStart', \[(.*?)\] \)/", file_get_contents ( $file ), $match );

    return preg_match_all ( "/'([A-Za-z]+)'/", $match [1] ?? '', $names ) ? $names [1] : [];

  }

  $options = array_unique ( array_merge (
    generateNames ( "$pad/options" ),
    generateStartOptions ( "$pad/inits/const.php" ),
    generateNames ( "$pad/handling/types" )
  ) );

  sort ( $options, SORT_STRING | SORT_FLAG_CASE );

  // label list, VS Code kind, detail, Sublime kind, Sublime annotation

  $groups = [
    [ generateNames ( "$pad/tags" ),                       'Keyword',    'PAD tag',       [ 'keyword',  't', 'Tag'      ], 'tag'           ],
    [ generateNames ( "$pad/functions" ),                  'Function',   'pipe function', [ 'function', 'f', 'Function' ], 'pipe function' ],
    [ generateNames ( "$pad/properties" ),                 'Property',   'property@tag',  [ 'variable', 'p', 'Property' ], 'property@tag'  ],
    [ array_values ( $options ),                           'EnumMember', 'option',        [ 'keyword',  'o', 'Option'   ], 'option'        ],
    [ array_map ( fn ( $t ) => "$t:", generateNames ( "$pad/types" ) ),
                                                           'Module',     'type prefix',   [ 'type',     'x', 'Prefix'   ], 'type prefix'   ],
    [ [ 'eq', 'ne', 'gt', 'lt', 'ge', 'le', 'and', 'or', 'xor', 'not', 'range' ],
                                                           'Operator',   'operator',      [ 'operator', '=', 'Operator' ], 'operator'      ],
    [ generateNames ( "$pad/sequence/types", TRUE ),       'Class',      'sequence type', [ 'type',     's', 'Sequence' ], 'sequence'      ],
  ];

  $code    = [];
  $sublime = [];

  foreach ( $groups as [ $labels, $kind, $detail, $subKind, $subNote ] )
    foreach ( $labels as $label ) {
      $code    [] = [ 'label' => $label, 'kind' => $kind, 'detail' => $detail ];
      $sublime [] = [ 'trigger'    => $label,
                      'contents'   => ( $detail == 'property@tag' ) ? "$label@" : $label,
                      'kind'       => $subKind,
                      'annotation' => $subNote ];
    }

  $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

  $files = [
    "$root/editors/vscode/pad/completions.json"     => json_encode ( $code, $flags ) . "\n",
    "$root/editors/lsp/completions.json"            => json_encode ( $code, $flags ) . "\n",
    "$root/editors/sublime/PAD.sublime-completions" => json_encode ( [ 'scope' => 'text.html.pad meta.embedded.pad', 'completions' => $sublime ], $flags ) . "\n",
  ];

  $check = in_array ( '--check', $argv ?? [] );
  $stale = 0;

  foreach ( $files as $file => $text ) {

    if ( @file_get_contents ( $file ) === $text )
      continue;

    $stale++;

    if ( $check )
      fwrite ( STDERR, "stale: " . substr ( $file, strlen ( $root ) + 1 ) . " - run php editors/generate.php\n" );
    else {
      file_put_contents ( $file, $text );
      echo "wrote " . substr ( $file, strlen ( $root ) + 1 ) . "\n";
    }

  }

  exit ( ( $check and $stale ) ? 1 : 0 );

?>
