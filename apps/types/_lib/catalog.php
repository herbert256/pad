<?php

  include_once COMMON . '_lib/subsystemCatalog.php';

  // The types application: a page per type of tag - the prefix before the colon of {pad:if},
  // {chart:pie}, {field:name} - and the indexes over them, as the tags application has a page
  // per tag. Nothing lists the types: they are the handlers of pad/types/, and what a page says
  // comes from the engine and its documentation - the line, group and syntax of _lib/types.php,
  // the type's section of docs/reference/TYPES.md, the comment at the top of its handler, the
  // required type prefix, and the tags it reaches, with a
  // link to their pages in the tags application - and from its examples, examples/<type>/:
  //
  //   examples/<type>/<x>.pad   an example, rendered live; its first line
  //                             {pad:meta title='...', about='...'/} names it
  //   examples/<type>/<x>.php   the data of an example that builds it in PHP
  //   examples/<type>/<x>.src   an example that is shown but never run - a database, a script

  // Every type, by name, in the order of its group and then by name: [ name, key, link,
  // about, group, syntax, comment, order, bare ].

  function typesCatalog () {

    static $catalog = NULL;

    if ( $catalog !== NULL )
      return $catalog;

    $info  = typesInfo ();
    $order = array_flip ( typesOrder () );
    $found = [];

    foreach ( padDirList ( PAD . 'types' ) ?: [] as $file ) {

      if ( ! str_ends_with ( $file, '.php' ) or $file [0] == '.' or $file [0] == '_' )
        continue;

      $name    = substr ( $file, 0, -4 );
      $comment = typesHandlerComment ( $name );

      list ( $about, $group, $syntax ) = $info [$name] ?? [ typesFirstSentence ( $comment ), 'tags', "{{$name}:name/}" ];

      $found [$name] = [
        'name'    => $name,
        'key'     => "$name:",
        'link'    => "type/$name",
        'about'   => $about,
        'group'   => isset ( typesGroups () [$group] ) ? $group : 'tags',
        'syntax'  => $syntax,
        'comment' => $comment,
        'prefix'  => TRUE,
        'language' => 'pad',
        'order'   => isset ( $order [$name] ) ? $order [$name] + 1 : 0,
        'bare'    => isset ( $order [$name] ) ];

    }

    foreach ( appSubsystemFamilies () as $name => $family ) {
      $sample = appSubsystemExample ( $name, $family ['example'] ?: ( padKinds ( $family ['directory'] ) [0] ?? '' ) );
      $found [$name] = [ 'name' => $name, 'key' => $family ['label'], 'link' => "type/$name",
                        'about' => $family ['about'], 'group' => 'kinds', 'syntax' => $sample ['source'],
                        'comment' => '', 'prefix' => FALSE, 'language' => $sample ['language'], 'order' => 0, 'bare' => FALSE ];
    }

    $groups = array_flip ( array_keys ( typesGroups () ) );

    uasort ( $found, fn ( $a, $b ) => $groups [ $a ['group'] ] <=> $groups [ $b ['group'] ] ?: strcmp ( $a ['name'], $b ['name'] ) );

    return $catalog = $found;

  }

  // The comment at the top of a type's handler, its lines joined.

  function typesHandlerComment ( $name ) {

    if ( ! preg_match ( '/\A<\?php\s*\n((?:[ \t]*\/\/[^\n]*\n)+)/', padFileGet ( PAD . "types/$name.php" ), $match ) )
      return '';

    return trim ( implode ( "\n", array_map ( fn ( $line ) => preg_replace ( '/^\s*\/\/ ?/', '', $line ), explode ( "\n", $match [1] ) ) ) );

  }

  function typesFirstSentence ( $text ) {

    $text = preg_replace ( '/\s+/', ' ', preg_replace ( '/^Type handler for /', '', $text ) );

    return ucfirst ( preg_match ( '/^(.+?[.:])(\s|$)/', $text, $match ) ? rtrim ( $match [1], ':' ) . '.' : $text );

  }

  // The section of a type in docs/reference/TYPES.md - from its ### heading to the next
  // heading or rule - as HTML.

  function typesDoc ( $name ) {

    static $text = NULL;

    $text ??= (string) @file_get_contents ( dirname ( APPS ) . '/docs/reference/TYPES.md' );

    if ( ! preg_match ( '/^### ' . preg_quote ( $name, '/' ) . '\s*\n(.*?)(?=^#{2,3} |^---|\z)/ms', $text, $match ) )
      return '';

    // A link of the section to another document - ../DATABASE.md - is to that file in the
    // repository: relative, on a page of this application it led nowhere.

    return preg_replace_callback ( '/href="(?![a-z]+:|#|\/)([^"]*\.md(?:#[^"]*)?)"/',
             fn ( $m ) => 'href="https://github.com/herbert256/pad/blob/master/' . typesDocPath ( 'docs/reference/' . $m [1] ) . '"',
             padMarkdown ( trim ( $match [1] ) ) );

  }

  // A path with its ../ and ./ resolved.

  function typesDocPath ( $path ) {

    $parts = [];

    foreach ( explode ( '/', $path ) as $part )
      if ( $part == '..' ) array_pop ( $parts );
      elseif ( $part != '.' and $part !== '' ) $parts [] = $part;

    return implode ( '/', $parts );

  }

  // The tags a type reaches that the engine has a list of: the built-in tags of pad:, the
  // kinds of a subsystem, the sequence types of sequence: and its plays, the sequence actions,
  // the pipe functions, the tags and snippets of _common - each [ key, link ], the link to its
  // page in the tags application where it has one. The other types reach what the page has:
  // its stores, its fields, its _tags.

  function typesMembers ( $type ) {

    global $padRoot;

    $tags  = "{$padRoot}tags/?tag/";
    $names = fn ( $dir, $extension = '.php' ) => array_values ( array_map ( fn ( $file ) => substr ( $file, 0, - strlen ( $extension ) ),
               array_filter ( padDirList ( $dir ) ?: [], fn ( $file ) => $file [0] != '.' and $file [0] != '_' and str_ends_with ( $file, $extension ) ) ) );

    if ( $type == 'pad' )
      return array_map ( fn ( $name ) => [ "pad:$name", "$tags$name" ], $names ( PAD . 'tags' ) );

    if ( isset ( padSubsystems [$type] ) )
      return array_map ( fn ( $kind ) => [ "$type:$kind", "$tags$type/$kind" ], padSubsystemTagKinds ( $type ) );

    if ( isset ( padSubsystemKinds [$type] ) )
      return array_map ( fn ( $kind ) => [ $kind, "{$padRoot}tags/?kind/$type/$kind" ], padKinds ( padSubsystemKinds [$type] ) );

    $selectors = in_array ( $type, padSequenceOperations, TRUE ) ? [ [ "$type:auto", "$tags$type/auto" ] ] : [];

    if ( in_array ( $type, [ 'sequence', 'flag', 'make', 'keep', 'remove' ] ) )
      return array_merge ( $selectors, array_map ( fn ( $kind ) => [ "$type:$kind", "{$tags}sequence/$kind" ],
                         array_values ( array_filter ( padDirList ( PAD . 'sequence/types' ) ?: [], fn ( $one ) => $one [0] != '.' and is_dir ( PAD . "sequence/types/$one" ) ) ) ) );

    if ( $type == 'action' )
      return array_merge ( $selectors, array_map ( fn ( $name ) => [ "action:$name", '' ], $names ( PAD . 'sequence/actions/types' ) ) );

    if ( $type == 'function' )
      return array_map ( fn ( $name ) => [ "function:$name", '' ], $names ( PAD . 'functions' ) );

    if ( $type == 'common' )
      return array_map ( fn ( $name ) => [ "common:$name", '' ],
                         array_values ( array_unique ( array_merge ( array_map ( fn ( $file ) => preg_replace ( '/\.[a-z]+$/', '', $file ),
                           array_filter ( array_merge ( padDirList ( COMMON . '_tags' ) ?: [], padDirList ( COMMON . '_include' ) ?: [] ), fn ( $file ) => $file [0] != '.' ) ) ) ) ) );

    return [];

  }

  // The examples of a type: [ page or file, title, about, run ].

  function typesExamples ( $name ) {

    if ( isset ( appSubsystemFamilies () [$name] ) ) {
      $family = appSubsystemFamilies () [$name];
      $sample = appSubsystemExample ( $name, $family ['example'] ?: ( padKinds ( $family ['directory'] ) [0] ?? '' ) );
      return [ $sample + [ 'page' => '', 'title' => $family ['label'], 'file' => '', 'php' => '', 'phpHtml' => '',
                          'padHtml' => padHighlightTokens ( $sample ['source'], $sample ['language'] ) ] ];
    }

    $examples = [];
    $files    = [];

    foreach ( ( is_dir ( APP . "examples/$name" ) ? padDirList ( APP . "examples/$name" ) : [] ) ?: [] as $file )
      if ( $file [0] != '.' and ( str_ends_with ( $file, '.pad' ) or str_ends_with ( $file, '.src' ) ) )
        $files [] = "examples/$name/$file";

    sort ( $files );

    foreach ( $files as $file ) {

      $run   = str_ends_with ( $file, '.pad' );
      $first = strtok ( (string) padFileGet ( APP . $file ), "\n" );
      $title = preg_match ( "/title='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : basename ( $file );
      $about = preg_match ( "/about='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : '';
      $page  = $run ? substr ( $file, 0, -4 ) : $file;
      $php   = $run && is_file ( APP . "$page.php" ) ? "$page.php" : '';

      $examples [] = [ 'page'    => $page,
                       'title'   => $title,
                       'about'   => $about,
                       'run'     => $run,
                       'source'  => '',
                       'language' => 'pad',
                       'file'    => $file,
                       'padHtml' => typesSource ( $file, 'pad' ),
                       'php'     => $php,
                       'phpHtml' => $php ? typesSource ( $php, 'php' ) : '' ];

    }

    return $examples;

  }

  function typesSource ( $file, $lang ) {

    $source = (string) padFileGet ( APP . $file );

    if ( $lang == 'pad' )
      $source = preg_replace ( '/\A\{pad:meta [^\n]*\}\R?/', '', $source );

    return padHighlightTokens ( trim ( $source ), $lang );

  }

  // What an index row shows of a type.

  function typesRow ( $type ) {

    return [ 'name'       => $type ['name'],
             'key'        => $type ['key'],
             'link'       => $type ['link'],
             'about'      => $type ['about'],
             'group'      => $type ['group'],
             'groupLabel' => typesGroups () [ $type ['group'] ] [0],
             'order'      => $type ['order'],
             'members'    => count ( typesMembers ( $type ['name'] ) ),
             'examples'   => count ( typesExamples ( $type ['name'] ) ) ];

  }

  function typesByGroup () {

    $rows = [];

    foreach ( typesGroups () as $key => list ( $label, $tagline ) ) {

      $types = [];

      foreach ( typesCatalog () as $type )
        if ( $type ['group'] == $key )
          $types [] = typesRow ( $type );

      if ( $types )
        $rows [] = [ 'key' => $key, 'label' => $label, 'tagline' => $tagline, 'types' => $types, 'count' => count ( $types ) ];

    }

    return $rows;

  }

  function typesByName () {

    $types = typesCatalog ();

    ksort ( $types );

    return array_values ( array_map ( 'typesRow', $types ) );

  }

  // The order a bare name is tried in: the types reached bare, by their place, then the types
  // reached by their prefix alone.

  function typesByOrder () {

    $bare = $prefixed = [];

    foreach ( typesCatalog () as $type )
      if ( ! $type ['prefix'] )
        continue;
      elseif ( $type ['bare'] )
        $bare [ $type ['order'] ] = typesRow ( $type );
      else
        $prefixed [] = typesRow ( $type );

    ksort ( $bare );

    return [ array_values ( $bare ), $prefixed ];

  }

  function typesCheatSheet () {

    $rows = [];

    foreach ( typesByGroup () as $group ) {

      $types = [];

      foreach ( $group ['types'] as $type )
        $types [] = $type + [ 'syntaxHtml' => padHighlightTokens ( typesCatalog () [ $type ['name'] ] ['syntax'], typesCatalog () [ $type ['name'] ] ['language'] ) ];

      $rows [] = [ 'key' => $group ['key'], 'label' => $group ['label'], 'types' => $types ];

    }

    return $rows;

  }

?>
