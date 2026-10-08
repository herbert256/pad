<?php

  // The showcase: a directory per tag, each a section of the home page, its examples a page
  // of their own. Nothing lists them - the catalog is read from the directories, so a tag
  // brings its examples by adding its directory:
  //
  //   <tag>/_about.txt     three lines: the title, one line about the tag, and its group
  //                        (Visuals, Text & data, Media, Interactive, Games)
  //   <tag>/<name>.pad     an example, its first line {meta title='...', about='...'} - the
  //                        title in the menu and over the cells, the line about it under
  //
  // The groups are the pulldowns of the menu, in the order of showcaseGroups; the tags in a
  // group and the examples of a tag go by name.

  function showcaseGroups () {

    return [ 'Visuals'     => 'Pictures drawn as SVG',
             'Text & data' => 'Rows, words and numbers',
             'Media'       => 'Images, video and documents',
             'Interactive' => 'Widgets without JavaScript of your own',
             'Games'       => 'Boards and puzzles' ];

  }

  function showcaseCatalog () {

    $groups = array_fill_keys ( array_keys ( showcaseGroups () ), [] );
    $dirs   = glob ( APP . '*/_about.txt' );

    sort ( $dirs );

    foreach ( $dirs as $about ) {

      $tag   = basename ( dirname ( $about ) );
      $lines = array_map ( 'trim', file ( $about, FILE_IGNORE_NEW_LINES ) );
      $group = $lines [2] ?? '';

      if ( ! isset ( $groups [$group] ) )
        $group = array_key_last ( $groups );

      $examples = [];
      $pages    = glob ( APP . "$tag/*.pad" );

      sort ( $pages );

      foreach ( $pages as $file ) {
        list ( $title, $text ) = showcaseMeta ( $file );
        $examples [ $tag . '/' . basename ( $file, '.pad' ) ] = [ $title, $text ];
      }

      if ( $examples )
        $groups [$group] [$tag] = [ $lines [0] ?? $tag, $lines [1] ?? '', $examples ];

    }

    return $groups;

  }

  // The title and the line about an example, from the {meta} on its first line.

  function showcaseMeta ( $file ) {

    $first = strtok ( (string) file_get_contents ( $file ), "\n" );
    $title = preg_match ( "/title='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : basename ( $file, '.pad' );
    $text  = preg_match ( "/about='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : '';

    return [ $title, $text ];

  }

?>
