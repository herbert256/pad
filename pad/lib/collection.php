<?php

  // Markdown collections - a folder of Markdown files as a data source, a small flat-file
  // CMS. _content/blog/hello.md is one row of the collection 'blog':
  //
  //   ---
  //   title: Hello PAD
  //   date: 2026-10-05
  //   ---
  //   First post ...
  //
  // The front-matter keys become the row's fields, and three are added: slug (the file
  // name without .md), body (the text after the front matter written as HTML by
  // padMarkdown) and source (that text as it was, for an excerpt). The rows come in file
  // name order; the handling options - sort='date DESC', first=10, where= - do the rest.
  //
  // padCollectionDir   the folder, looked up like a _data file: the page's directory first,
  //                    then each parent up to the application root, then the _common
  //                    application when $padCommon is on
  // padCollection      the rows - all of them, or the one a slug names
  // padFrontMatter     splits a file into its front matter and its body
  // padFrontMatterRead reads the front matter: the PHP yaml extension when it is loaded,
  //                    else padFrontMatterLines, a reader for the flat key: value form

  function padCollectionDir ( $name ) {

    global $padCommon;

    if ( ! preg_match ( '/^[A-Za-z0-9_-]+(\/[A-Za-z0-9_-]+)*$/', $name ) )
      return '';

    foreach ( padDirs () as $dir )
      if ( is_dir ( APP2 . $dir . "_content/$name" ) )
        return APP2 . $dir . "_content/$name/";

    if ( $padCommon and is_dir ( COMMON . "_content/$name" ) )
      return COMMON . "_content/$name/";

    return '';

  }

  // The rows of a collection. A slug - the post page's ?slug= - picks one file without
  // reading the others; a slug that is not a plain file name, or names no file, answers no
  // rows, so the page can show its @else@. $html lets raw HTML in the bodies through, for
  // a collection whose files the site's authors write; by default it is escaped.

  function padCollection ( $name, $slug = '', $html = FALSE ) {

    $dir = padCollectionDir ( (string) $name );

    if ( ! $dir ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "there is no collection named '" . padMakeSafe ( $name, 40 ) . "' - a _content/" . padMakeSafe ( $name, 40 ) . "/ directory" );
      return [];
    }

    if ( $slug !== '' and $slug !== NULL and $slug !== FALSE ) {

      if ( ! preg_match ( '/^[A-Za-z0-9_-][A-Za-z0-9_.-]*$/', (string) $slug ) or ! is_file ( "$dir$slug.md" ) )
        return [];

      $files = [ "$dir$slug.md" ];

    } else {

      $files = glob ( $dir . '*.md' ) ?: [];
      sort ( $files );

    }

    $rows = [];

    foreach ( $files as $file ) {

      list ( $meta, $source ) = padFrontMatter ( (string) file_get_contents ( $file ), $file );

      $meta ['slug']   = basename ( $file, '.md' );
      $meta ['body']   = padMarkdown ( $source, $html );
      $meta ['source'] = trim ( $source );

      $rows [] = $meta;

    }

    return $rows;

  }

  // The front matter is the block between a first line of --- and the next line of --- or
  // ...; a file without it is all body.

  function padFrontMatter ( $text, $file = '' ) {

    $text = str_replace ( [ "\r\n", "\r" ], "\n", $text );
    $text = preg_replace ( '/^\xEF\xBB\xBF/', '', $text );

    if ( ! preg_match ( '/^---[ \t]*\n(.*?)\n?(?:---|\.\.\.)[ \t]*(?:\n|$)/s', $text, $m ) )
      return [ [], $text ];

    return [ padFrontMatterRead ( $m [1], $file ), substr ( $text, strlen ( $m [0] ) ) ];

  }

  function padFrontMatterRead ( $yaml, $file ) {

    if ( trim ( $yaml ) === '' )
      return [];

    if ( function_exists ( 'yaml_parse' ) ) {
      set_error_handler ( function () { return TRUE; } );
      $meta = yaml_parse ( "---\n$yaml\n" );
      restore_error_handler ();
    } else
      $meta = padFrontMatterLines ( $yaml );

    if ( ! is_array ( $meta ) ) {
      padError ( "the front matter of " . basename ( $file ) . " is not a list of key: value lines" );
      return [];
    }

    return $meta;

  }

  // Without the yaml extension: one key: value per line, a quoted or plain scalar, a
  // [a, b] list, or a key: followed by - item lines. true, false, null and numbers are
  // read as such, a date stays text - what yaml_parse makes of the same lines.

  function padFrontMatterLines ( $yaml ) {

    $meta = [];
    $last = '';

    foreach ( explode ( "\n", $yaml ) as $line ) {

      if ( trim ( $line ) === '' or str_starts_with ( ltrim ( $line ), '#' ) )
        continue;

      if ( $last !== '' and preg_match ( '/^\s+-\s*(.*)$/', $line, $m ) ) {
        if ( ! is_array ( $meta [$last] ) )
          $meta [$last] = [];
        $meta [$last] [] = padFrontMatterScalar ( $m [1] );
        continue;
      }

      if ( ! preg_match ( '/^([A-Za-z_][A-Za-z0-9_-]*)\s*:\s*(.*)$/', $line, $m ) )
        return FALSE;

      $last  = $m [1];
      $value = trim ( $m [2] );

      if ( preg_match ( '/^\[(.*)\]$/', $value, $list ) )
        $meta [$last] = trim ( $list [1] ) === '' ? [] : array_map ( 'padFrontMatterScalar', explode ( ',', $list [1] ) );
      else
        $meta [$last] = ( $value === '' ) ? NULL : padFrontMatterScalar ( $value );

    }

    return $meta;

  }

  function padFrontMatterScalar ( $value ) {

    $value = trim ( $value );

    if ( preg_match ( '/^"(.*)"$/', $value, $m ) ) return stripcslashes ( $m [1] );
    if ( preg_match ( "/^'(.*)'$/", $value, $m ) ) return str_replace ( "''", "'", $m [1] );

    $value = preg_replace ( '/\s+#.*$/', '', $value );
    $lower = strtolower ( $value );

    if ( in_array ( $lower, [ 'true', 'yes', 'on' ] ) )   return TRUE;
    if ( in_array ( $lower, [ 'false', 'no', 'off' ] ) )  return FALSE;
    if ( in_array ( $lower, [ 'null', '~', '' ] ) )       return NULL;
    if ( preg_match ( '/^-?[0-9]+$/', $value ) )          return (int) $value;
    if ( preg_match ( '/^-?[0-9]*\.[0-9]+$/', $value ) )  return (float) $value;

    return $value;

  }

?>
