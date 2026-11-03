<?php

  // Documentation, highlighted source and example metadata shared by the Types and
  // Subsystems reference applications. Example paths are relative to the chosen app.

  function appReferenceHandlerComment ( $name ) {

    if ( ! preg_match ( '/\A<\?php\s*\n((?:[ \t]*\/\/[^\n]*\n)+)/', padFileGet ( PAD . "types/$name.php" ), $match ) )
      return '';

    return trim ( implode ( "\n", array_map ( fn ( $line ) => preg_replace ( '/^\s*\/\/ ?/', '', $line ), explode ( "\n", $match [1] ) ) ) );

  }

  function appReferenceFirstSentence ( $text ) {

    $text = preg_replace ( '/\s+/', ' ', preg_replace ( '/^Type handler for /', '', $text ) );

    return ucfirst ( preg_match ( '/^(.+?[.:])(\s|$)/', $text, $match ) ? rtrim ( $match [1], ':' ) . '.' : $text );

  }

  function appReferenceDoc ( $name ) {

    static $text = NULL;

    $text ??= (string) @file_get_contents ( dirname ( APPS ) . '/docs/reference/TYPES.md' );

    if ( ! preg_match ( '/^### ' . preg_quote ( $name, '/' ) . '\s*\n(.*?)(?=^#{2,3} |^---|\z)/ms', $text, $match ) )
      return '';

    // A link of the section to another document - ../DATABASE.md - is to that file in the
    // repository: relative, on a page of this application it led nowhere.

    return preg_replace_callback ( '/href="(?![a-z]+:|#|\/)([^"]*\.md(?:#[^"]*)?)"/',
             fn ( $m ) => 'href="https://github.com/herbert256/pad/blob/master/' . appReferenceDocPath ( 'docs/reference/' . $m [1] ) . '"',
             padMarkdown ( trim ( $match [1] ) ) );

  }

  function appReferenceDocPath ( $path ) {

    $parts = [];

    foreach ( explode ( '/', $path ) as $part )
      if ( $part == '..' ) array_pop ( $parts );
      elseif ( $part != '.' and $part !== '' ) $parts [] = $part;

    return implode ( '/', $parts );

  }

  function appReferenceExamples ( $name, $app ) {

    $examples = [];
    $files    = [];

    foreach ( ( is_dir ( APPS . "$app/" . "examples/$name" ) ? padDirList ( APPS . "$app/" . "examples/$name" ) : [] ) ?: [] as $file )
      if ( $file [0] != '.' and ( str_ends_with ( $file, '.pad' ) or str_ends_with ( $file, '.src' ) ) )
        $files [] = "examples/$name/$file";

    sort ( $files );

    foreach ( $files as $file ) {

      $run   = str_ends_with ( $file, '.pad' );
      $first = strtok ( (string) padFileGet ( APPS . "$app/" . $file ), "\n" );
      $title = preg_match ( "/title='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : basename ( $file );
      $about = preg_match ( "/about='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : '';
      $page  = $run ? substr ( $file, 0, -4 ) : $file;
      $php   = $run && is_file ( APPS . "$app/" . "$page.php" ) ? "$page.php" : '';

      $examples [] = [ 'page'    => $page,
                       'title'   => $title,
                       'about'   => $about,
                       'run'     => $run,
                       'source'  => '',
                       'language' => 'pad',
                       'file'    => $file,
                       'padHtml' => appReferenceSource ( $file, 'pad', $app ),
                       'php'     => $php,
                       'phpHtml' => $php ? appReferenceSource ( $php, 'php', $app ) : '' ];

    }

    return $examples;

  }

  function appReferenceSource ( $file, $lang, $app ) {

    $source = (string) padFileGet ( APPS . "$app/" . $file );

    if ( $lang == 'pad' )
      $source = preg_replace ( '/\A\{pad:meta [^\n]*\}\R?/', '', $source );

    return padHighlightTokens ( trim ( $source ), $lang );

  }

?>
