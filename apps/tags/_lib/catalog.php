<?php

  include_once COMMON . '_lib/subsystemCatalog.php';

  // The tags application: a page per tag, and the indexes over them - every built-in tag,
  // listed with its type prefix, {pad:if}, and every kind of the subsystems and every
  // sequence type, {chart:pie}, {sequence:fibonacci}, read from the engine (_lib/kinds.php).
  // Nothing lists the built-in tags - the catalog is read from the files, so a tag joins
  // every index by adding its files:
  //
  //   tag/<name>.php           what the indexes read: $tagAbout (the one line), $tagGroup
  //                            (a key of tagsGroups), $tagForm ('single', 'pair' or 'both'),
  //                            $tagSyntax (the forms, one per line), $tagParms (the
  //                            positional parameters), $tagOptions (the named options of
  //                            this tag - not the handling options every tag takes) and
  //                            $tagSee (related tags)
  //   tag/<name>.pad           the longer description
  //   examples/<name>/<x>.pad  an example, rendered live; its first line
  //                            {pad:meta title='...', about='...'} names it
  //   examples/<name>/<x>.php  the data of an example that builds it in PHP
  //   examples/<name>/<x>.src  an example that is shown but never run - a redirect, an
  //                            exit, a mail - first line as above
  //
  // Groups go in the order of tagsGroups, tags by name, examples by file name.

  function tagsGroups () {

    return [
      'conditions' => [ 'Conditions',            'Choosing what renders'                          ],
      'loops'      => [ 'Loops and trees',       'Repeating, stopping and recursing'              ],
      'values'     => [ 'Variables and stores',  'Setting, counting and keeping values'           ],
      'database'   => [ 'Database',              'Rows and values straight from SQL'              ],
      'pages'      => [ 'Pages and execution',   'Other pages, code and live parts'               ],
      'navigation' => [ 'Navigation',            'Redirects, page links and the site map'         ],
      'forms'      => [ 'Forms and security',    'Fields, validation, tokens and messages'        ],
      'access'     => [ 'Users and access',      'Who is logged in and what they may do'          ],
      'files'      => [ 'Files',                 'Reading the file system'                        ],
      'network'    => [ 'Network and mail',      'Remote data and outgoing mail'                  ],
      'output'     => [ 'Output control',        'What is sent, how and when'                     ],
      'text'       => [ 'Text and formatting',   'Markdown, code, diffs, dates and words'         ],
      'graphics'   => [ 'Charts and codes',      'Charts, maps, diagrams and barcodes as SVG'     ],
      'pictures'   => [ 'Pictures and media',    'Avatars, icons, ratings, images and video'      ],
      'widgets'    => [ 'Widgets',               'Tabs, modals, tables and more without JavaScript' ],
      'games'      => [ 'Games',                 'Boards and puzzles'                             ],
      'layout'     => [ 'Layouts and components','Extending, blocks, slots, stacks and fragments' ],
      'debug'      => [ 'Debugging and errors',  'Looking inside, and stopping on purpose'        ],
      'sequences'  => [ 'Sequences',             'Number sequences and what is done to them'      ],
    ] + array_combine ( array_map ( fn ( $type ) => "kind-$type", array_keys ( tagsKindTypes () ) ),
                        array_map ( fn ( $one ) => [ $one [0], $one [1] ], tagsKindTypes () ) )
      + array_combine ( array_map ( fn ( $key ) => "subsystem-$key", array_keys ( appSubsystemFamilies () ) ),
                        array_map ( fn ( $one ) => [ $one ['label'], $one ['about'] ], appSubsystemFamilies () ) );

  }

  function tagsForms () {

    return [
      'single' => [ 'Single tags', 'Written alone - {property:name ...} - and replaced by what they answer' ],
      'pair'   => [ 'Tag pairs',   'Written around content - {property:name ...} ... {/property:name}'                ],
      'both'   => [ 'Either form', 'Alone or as a pair - the pair form gives them content to use'   ],
      'usage'  => [ 'Options and helpers', 'Subsystem kinds selected by an option, parameter or helper call' ],
    ];

  }

  // Every tag, by its name with its type prefix - pad:if, chart:pie: [ name, type, key, link,
  // about, group, form, syntax, parms, options, see ], the built-in tags first, then the kinds
  // of the subsystems and the sequence types.

  function tagsCatalog () {

    static $catalog = NULL;

    if ( $catalog !== NULL )
      return $catalog;

    $catalog = [];
    $files   = tagsFiles ( 'tag', [ '.php' ] );

    foreach ( $files as $file )
      if ( basename ( $file ) [0] != '_' and padTagBuiltIn ( basename ( $file, '.php' ) ) )
        $catalog [ 'pad:' . basename ( $file, '.php' ) ] = tagsRead ( $file );

    return $catalog += tagsKinds () + appSubsystemCatalog ();

  }

  function tagsRead ( $file ) {

    $tagAbout = $tagGroup = $tagForm = $tagSyntax = '';
    $tagParms = $tagOptions = $tagSee = [];

    include $file;

    $name = basename ( $file, '.php' );

    return [ 'name'    => $name,
             'type'    => 'pad',
             'key'     => "pad:$name",
             'link'    => "tag/$name",
             'about'   => $tagAbout,
             'group'   => isset ( tagsGroups () [$tagGroup] ) ? $tagGroup : 'debug',
             'form'    => isset ( tagsForms  () [$tagForm]  ) ? $tagForm  : 'single',
             'syntax'  => trim ( $tagSyntax ),
             'parms'   => $tagParms,
             'options' => $tagOptions,
             'see'     => array_map ( fn ( $see ) => str_contains ( $see, ':' ) ? $see : ( tagsRenderingTags () [$see] ?? "pad:$see" ), $tagSee ) ];

  }

  // The examples of a tag: [ page or file, title, about, run ] - kept for the request: the
  // index counts every tag's examples and its rows count them again, two globs and a read of
  // every example file per tag each time, two thirds of the index page.

  function tagsExamples ( $name ) {

    static $memo = [];

    if ( isset ( $memo [$name] ) )
      return $memo [$name];

    $examples = [];
    $files    = tagsFiles ( "examples/$name", [ '.pad', '.src' ] );

    // By their numbers as numbers: 10-x comes after 9-y, where by their characters it stood
    // between 1-x and 2-y.

    usort ( $files, fn ( $a, $b ) => strnatcmp ( basename ( $a ), basename ( $b ) ) );

    foreach ( $files as $file ) {

      $run   = str_ends_with ( $file, '.pad' );
      $first = strtok ( (string) file_get_contents ( $file ), "\n" );
      $title = preg_match ( "/title='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : basename ( $file );
      $about = preg_match ( "/about='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : '';
      $page  = substr ( $file, strlen ( APP ), $run ? -4 : NULL );

      $examples [] = [ 'page' => $page, 'title' => $title, 'about' => $about, 'run' => $run ];

    }

    return $memo [$name] = $examples;

  }

  // The files of a directory of the application with one of the extensions, as the globs
  // tag/*.php and examples/<name>/*.pad found them - sorted, no dot files - from the listing
  // the engine keeps between requests (padDirList): every page read the 287 entries of tag/,
  // and an index the examples directory of every tag twice.

  function tagsFiles ( $dir, $extensions ) {

    $files = [];

    if ( ! is_dir ( APP . $dir ) )
      return $files;

    foreach ( padDirList ( APP . $dir ) ?: [] as $file )
      if ( $file [0] != '.' )
        foreach ( $extensions as $extension )
          if ( str_ends_with ( $file, $extension ) )
            $files [] = APP . "$dir/$file";

    sort ( $files );

    return $files;

  }

  // How many examples a tag has - what an index row shows - without reading each one's
  // first line for its title, which only the tag's own page shows.

  function tagsExampleCount ( $name ) {

    return count ( tagsFiles ( "examples/$name", [ '.pad', '.src' ] ) );

  }

  // The examples of an entry of the catalog: a built-in tag's files, the one of a kind.

  function tagsExampleTotal ( $tag ) {

    return ( $tag ['type'] == 'pad' ) ? tagsExampleCount ( $tag ['name'] ) : 1;

  }

  // A source file of the application as highlighted HTML, its {pad:meta} line left out: that
  // line belongs to the catalog, not to the example.

  function tagsSource ( $file, $lang ) {

    $source = (string) file_get_contents ( APP . $file );

    if ( $lang == 'pad' )
      $source = preg_replace ( '/\A\{pad:meta [^\n]*\}\R?/', '', $source );

    return padHighlightTokens ( trim ( $source ), $lang );

  }

  // The rows of the A-Z index: a row per letter of the names after their prefix, its tags
  // inside by name - {pad:if} beside {sequence:icosahedral}.

  function tagsLetters () {

    $letters = [];
    $tags    = tagsCatalog ();

    uasort ( $tags, fn ( $a, $b ) => strcasecmp ( $a ['name'], $b ['name'] ) ?: strcmp ( $a ['key'], $b ['key'] ) );

    foreach ( $tags as $tag )
      $letters [ strtoupper ( $tag ['name'] [0] ) ] [] = tagsRow ( $tag );

    ksort ( $letters );

    $rows = [];

    foreach ( $letters as $letter => $tags )
      $rows [] = [ 'letter' => $letter, 'tags' => $tags, 'count' => count ( $tags ) ];

    return $rows;

  }

  // The rows of the group index: a row per group, its tags inside.

  function tagsByGroup () {

    $rows = [];

    foreach ( tagsGroups () as $key => list ( $label, $tagline ) ) {

      $tags = [];

      foreach ( tagsCatalog () as $tag )
        if ( $tag ['group'] == $key )
          $tags [] = tagsRow ( $tag );

      if ( $tags )
        $rows [] = [ 'key' => $key, 'label' => $label, 'tagline' => $tagline, 'tags' => $tags, 'count' => count ( $tags ) ];

    }

    return $rows;

  }

  // The rows of the form index: single tags, pairs, and the tags that are both.

  function tagsByForm () {

    $rows = [];

    foreach ( tagsForms () as $key => list ( $label, $tagline ) ) {

      $tags = [];

      foreach ( tagsCatalog () as $tag )
        if ( $tag ['form'] == $key )
          $tags [] = tagsRow ( $tag );

      $rows [] = [ 'key' => $key, 'label' => $label, 'tagline' => $tagline, 'tags' => $tags, 'count' => count ( $tags ) ];

    }

    return $rows;

  }

  // The rows of the option index: every named option, the tags that take it and what it
  // does there.

  function tagsByOption () {

    $options = [];

    $catalog = tagsCatalog ();
    foreach ( array_merge ( array_keys ( padSubsystems ), padSequenceOperations ) as $type )
      if ( is_file ( APPS . "tags/tag/$type.php" ) && ! padTagBuiltIn ( $type ) && ! isset ( tagsRenderingTags () [$type] ) )
        $catalog ["$type:*"] = tagsRead ( APPS . "tags/tag/$type.php" );

    foreach ( $catalog as $key => $tag )
      foreach ( $tag ['options'] as $option => $meaning )
        $options [ (string) $option ] [] = [ 'name' => $tag ['name'], 'key' => $key, 'label' => $tag ['label'] ?? '{' . $key . '}', 'link' => $tag ['link'], 'meaning' => $meaning ];

    ksort ( $options, SORT_NATURAL | SORT_FLAG_CASE );

    $rows = [];

    foreach ( $options as $option => $tags )
      $rows [] = [ 'option' => $option, 'tags' => $tags, 'count' => count ( $tags ) ];

    return $rows;

  }

  // The rows of the cheat sheet: every tag with its syntax, highlighted, in group order.

  function tagsCheatSheet () {

    $rows = [];

    foreach ( tagsByGroup () as $group ) {

      $tags = [];

      foreach ( $group ['tags'] as $tag )
        $tags [] = $tag + [ 'syntaxHtml' => padHighlightTokens ( tagsCatalog () [ $tag ['key'] ] ['syntax'], tagsCatalog () [ $tag ['key'] ] ['language'] ?? 'pad' ) ];

      $rows [] = [ 'key' => $group ['key'], 'label' => $group ['label'], 'tags' => $tags ];

    }

    return $rows;

  }

  // What an index row shows of a tag.

  function tagsRow ( $tag ) {

    return [ 'name'       => $tag ['name'],
             'key'        => $tag ['key'],
             'label'      => $tag ['label'] ?? '{' . $tag ['key'] . '}',
             'link'       => $tag ['link'],
             'about'      => $tag ['about'],
             'group'      => $tag ['group'],
             'groupLabel' => tagsGroups () [ $tag ['group'] ] [0],
             'form'       => $tag ['form'],
             'examples'   => tagsExampleTotal ( $tag ) ];

  }

?>
