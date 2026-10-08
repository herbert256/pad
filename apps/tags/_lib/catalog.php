<?php

  // The tags application: a page per built-in tag, and the indexes over them. Nothing
  // lists the tags - the catalog is read from the files, so a tag joins every index by
  // adding its files:
  //
  //   tag/<name>.php           what the indexes read: $tagAbout (the one line), $tagGroup
  //                            (a key of tagsGroups), $tagForm ('single', 'pair' or 'both'),
  //                            $tagSyntax (the forms, one per line), $tagParms (the
  //                            positional parameters), $tagOptions (the named options of
  //                            this tag - not the handling options every tag takes) and
  //                            $tagSee (related tags)
  //   tag/<name>.pad           the longer description
  //   examples/<name>/<x>.pad  an example, rendered live; its first line
  //                            {meta title='...', about='...'} names it
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
    ];

  }

  function tagsForms () {

    return [
      'single' => [ 'Single tags', 'Written alone - {name ...} - and replaced by what they answer' ],
      'pair'   => [ 'Tag pairs',   'Written around content - {name ...} ... {/name}'                ],
      'both'   => [ 'Either form', 'Alone or as a pair - the pair form gives them content to use'   ],
    ];

  }

  // Every tag, by name: [ name, about, group, form, syntax, parms, options, see ].

  function tagsCatalog () {

    static $catalog = NULL;

    if ( $catalog !== NULL )
      return $catalog;

    $catalog = [];
    $files   = glob ( APP . 'tag/*.php' );

    sort ( $files );

    foreach ( $files as $file )
      if ( basename ( $file ) [0] != '_' )
        $catalog [ basename ( $file, '.php' ) ] = tagsRead ( $file );

    return $catalog;

  }

  function tagsRead ( $file ) {

    $tagAbout = $tagGroup = $tagForm = $tagSyntax = '';
    $tagParms = $tagOptions = $tagSee = [];

    include $file;

    return [ 'name'    => basename ( $file, '.php' ),
             'about'   => $tagAbout,
             'group'   => isset ( tagsGroups () [$tagGroup] ) ? $tagGroup : 'debug',
             'form'    => isset ( tagsForms  () [$tagForm]  ) ? $tagForm  : 'single',
             'syntax'  => trim ( $tagSyntax ),
             'parms'   => $tagParms,
             'options' => $tagOptions,
             'see'     => $tagSee ];

  }

  // The examples of a tag: [ page or file, title, about, run ].

  function tagsExamples ( $name ) {

    $examples = [];
    $files    = array_merge ( glob ( APP . "examples/$name/*.pad" ), glob ( APP . "examples/$name/*.src" ) );

    usort ( $files, fn ( $a, $b ) => strcmp ( basename ( $a ), basename ( $b ) ) );

    foreach ( $files as $file ) {

      $run   = str_ends_with ( $file, '.pad' );
      $first = strtok ( (string) file_get_contents ( $file ), "\n" );
      $title = preg_match ( "/title='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : basename ( $file );
      $about = preg_match ( "/about='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : '';
      $page  = substr ( $file, strlen ( APP ), $run ? -4 : NULL );

      $examples [] = [ 'page' => $page, 'title' => $title, 'about' => $about, 'run' => $run ];

    }

    return $examples;

  }

  // A source file of the application as highlighted HTML, its {meta} line left out: that
  // line belongs to the catalog, not to the example.

  function tagsSource ( $file, $lang ) {

    $source = (string) file_get_contents ( APP . $file );

    if ( $lang == 'pad' )
      $source = preg_replace ( '/\A\{meta [^\n]*\}\R?/', '', $source );

    return padHighlightTokens ( trim ( $source ), $lang );

  }

  // The rows of the A-Z index: a row per letter, its tags inside.

  function tagsLetters () {

    $letters = [];

    foreach ( tagsCatalog () as $name => $tag )
      $letters [ strtoupper ( $name [0] ) ] [] = tagsRow ( $tag );

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

    foreach ( tagsCatalog () as $name => $tag )
      foreach ( $tag ['options'] as $option => $meaning )
        $options [ (string) $option ] [] = [ 'name' => $name, 'meaning' => $meaning ];

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
        $tags [] = $tag + [ 'syntaxHtml' => padHighlightTokens ( tagsCatalog () [ $tag ['name'] ] ['syntax'], 'pad' ) ];

      $rows [] = [ 'key' => $group ['key'], 'label' => $group ['label'], 'tags' => $tags ];

    }

    return $rows;

  }

  // What an index row shows of a tag.

  function tagsRow ( $tag ) {

    return [ 'name'       => $tag ['name'],
             'about'      => $tag ['about'],
             'group'      => $tag ['group'],
             'groupLabel' => tagsGroups () [ $tag ['group'] ] [0],
             'form'       => $tag ['form'],
             'examples'   => count ( tagsExamples ( $tag ['name'] ) ) ];

  }

?>
