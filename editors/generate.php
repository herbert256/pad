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
  //   tree-sitter-pad/queries/highlights.scm   the Tree-sitter grammar's built-in names, the
  //                                            part between its generated markers
  //   tree-sitter-pad/helix/highlights.scm     the same file in Helix's capture names
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

  // The Tree-sitter highlights name the built-ins: a tag name is a keyword when pad/tags has
  // it (or it is one of the clauses the level engine reads, else / elseif / when) and an
  // application's tag otherwise; a pipe function is built in or the application's; a bare
  // word among a tag's arguments is an option or a word operator. Each pair of patterns is
  // exclusive - any-of and not-any-of over one list - so no editor's rule for overlapping
  // patterns decides the colour. The hand-written rest of the file is kept as it is.

  $treeSitter = "$root/editors/tree-sitter-pad";
  $tsBegin    = '; ---- generated by editors/generate.php from pad/ - do not edit by hand ----';
  $tsEnd      = '; ---- end of generated ----';

  function generateQuoted ( $names ) {

    $lines = [];

    foreach ( array_chunk ( $names, 8 ) as $chunk )
      $lines [] = '    ' . implode ( ' ', array_map ( fn ( $n ) => "\"$n\"", $chunk ) );

    return implode ( "\n", $lines );

  }

  // Two patterns over one list - the names in it, and when $out is given the names not in
  // it - written between $head and $tail: '(tag_name name: (identifier)' and ')' put the
  // predicate inside the node, '((function_name)' and ')' after a leaf.

  function generatePair ( $head, $tail, $in, $out, $names ) {

    $list = generateQuoted ( $names );
    $text = "$head @$in\n  (#any-of? @$in\n$list)$tail\n\n";

    if ( $out )
      $text .= "$head @$out\n  (#not-any-of? @$out\n$list)$tail\n\n";

    return $text;

  }

  $tsTags = array_unique ( array_merge ( generateNames ( "$pad/tags" ), [ 'else', 'elseif', 'when' ] ) );
  sort ( $tsTags, SORT_STRING | SORT_FLAG_CASE );

  $tsGenerated =
      generatePair ( "(tag_name\n  name: (identifier)", ')', 'keyword', 'tag', $tsTags )
    . generatePair ( '((function_name)', ')', 'function.builtin', 'function.call', generateNames ( "$pad/functions" ) )
    . generatePair ( "(arguments\n  (identifier)", ')', 'attribute', '', array_values ( $options ) )
    . generatePair ( "(arguments\n  (identifier)", ')', 'keyword.operator', '',
                     [ 'eq', 'ne', 'gt', 'lt', 'ge', 'le', 'and', 'or', 'xor', 'not', 'range' ] );

  $tsQuery = @file_get_contents ( "$treeSitter/queries/highlights.scm" );

  if ( $tsQuery !== FALSE and ( $tsAt = strpos ( $tsQuery, $tsBegin ) ) !== FALSE
       and ( $tsStop = strpos ( $tsQuery, $tsEnd, $tsAt ) ) !== FALSE ) {

    $tsQuery = substr ( $tsQuery, 0, $tsAt ) . "$tsBegin\n\n" . $tsGenerated . substr ( $tsQuery, $tsStop );

    // Helix names a few captures differently; everything else is the same file.

    $tsHelix = preg_replace_callback ( '/@[a-z][a-z.]*/', fn ( $m ) => [
      '@module'        => '@namespace',
      '@number'        => '@constant.numeric',
      '@property'      => '@variable.other.member',
      '@function.call' => '@function',
    ] [$m [0]] ?? $m [0], $tsQuery );

    $tsHelix = preg_replace ( '/\A(;[^\n]*\n)+/',
      "; PAD templates - highlights for Helix: queries/highlights.scm in Helix's capture names,\n"
      . "; written by editors/generate.php - do not edit by hand.\n", $tsHelix );

    $files ["$treeSitter/queries/highlights.scm"] = $tsQuery;
    $files ["$treeSitter/helix/highlights.scm"]   = $tsHelix;

  }

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
