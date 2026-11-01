<?php

  // The notations of the engine, as the engine reads them - the files of
  // pad/subs/notations/types/ (padKinds) - each with what its own file says of it: the comment
  // at its top, without the name or the syntax it starts with, and its first sentence as the
  // line of its card. A notation added to the engine is here without a change to this
  // application: its card, its place in the menu and [kind].pad, which shows it with the
  // example of its comment until it has a page of its own.

  function notationsCatalog () {

    static $catalog = NULL;

    if ( $catalog !== NULL )
      return $catalog;

    $catalog = [];

    foreach ( padKinds ( 'notations' ) as $kind ) {

      $comment = [];

      foreach ( file ( PAD . "subs/notations/types/$kind.php", FILE_IGNORE_NEW_LINES ) as $line ) {
        if ( preg_match ( '/^\s*\/\/\s?(.*)$/', $line, $match ) ) {
          if ( trim ( $match [1] ) === '' )
            break;
          $comment [] = trim ( $match [1] );
        } elseif ( $comment )
          break;
      }

      // The first paragraph, without the kind's name or its syntax in front of it.

      $text = implode ( ' ', $comment );
      $text = preg_replace ( '/^' . preg_quote ( $kind, '/' ) . '\s+-\s+/', '', $text );
      $text = preg_replace ( '/^\{.*?\}\s+-\s+/', '', $text );
      $text = ucfirst ( $text );

      $first = preg_split ( '/(?<=[.:;])\s/', $text, 2 ) [0];
      $line  = ucfirst ( rtrim ( $first, '.:; ' ) ) . '.';

      $catalog [$kind] = [ 'kind' => $kind, 'name' => notationsName ( $kind ), 'line' => $line, 'text' => $text,
                           'tag'  => notationsTag ( $kind ) ];

    }

    return $catalog;

  }

  // The name a notation is shown under.

  function notationsName ( $kind ) {

    return [ 'math' => 'Mathematics', 'chem' => 'Chemistry', 'abc' => 'ABC music', 'chord' => 'Chord boxes',
             'piano' => 'Piano keys', 'braille' => 'Braille', 'morse' => 'Morse code', 'semaphore' => 'Semaphore',
             'signals' => 'Signal flags', 'chess' => 'Chess', 'go' => 'Go', 'draughts' => 'Draughts' ] [$kind] ?? ucfirst ( $kind );

  }

  // How the notation is written on its card: bare when the engine reaches it bare - chord
  // is the chord diagram of the charts bare, so the guitar chord is {notation:chord}.

  function notationsTag ( $kind ) {

    return ( padSubsystemBare ( $kind ) === 'notation' ) ? "{{$kind}}" : "{notation:$kind}";

  }

  // The examples of a notation, _examples/<kind>/<n>-<slug>.pad - their first line
  // {meta title='...', about='...'} names them - each with its template as it is shown (the
  // meta line left out), coloured, and the icons that take it to the playground and to the
  // live application (_common/_lib/playground.php).

  function notationsExamples ( $kind ) {

    include_once COMMON . '_lib/playground.php';

    $examples = [];
    $dir      = APP . "_examples/$kind";

    if ( ! is_dir ( $dir ) )
      return $examples;

    $files = array_filter ( padDirList ( $dir ) ?: [], fn ( $file ) => str_ends_with ( $file, '.pad' ) );

    sort ( $files );

    foreach ( $files as $file ) {

      $source   = (string) file_get_contents ( "$dir/$file" );
      $first    = strtok ( $source, "\n" );
      $title    = preg_match ( "/title='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : basename ( $file, '.pad' );
      $about    = preg_match ( "/about='((?:[^'\\\\]|\\\\.)*)'/", $first, $m ) ? stripslashes ( $m [1] ) : '';
      $template = trim ( preg_replace ( '/\A\{meta [^\n]*\}\R?/', '', $source ) );

      $examples [] = [ 'slug'     => preg_replace ( '/^\d+-/', '', basename ( $file, '.pad' ) ),
                       'title'    => $title,
                       'about'    => $about,
                       'template' => $template,
                       'source'   => padHighlightTokens ( $template, 'pad' ),
                       'play'     => padPlayLink ( "$template\n" ) ];

    }

    return $examples;

  }

?>
