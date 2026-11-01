<?php

  // Renders the template the playground posts, with the data posted beside it: a JSON object,
  // each top-level key a variable of the template, as a page's .php would set it - or, with
  // mode=php, PHP code that is such a .php. The template runs in a nested pass of this
  // request (padCode) under the application's limits - no PHP functions, no request values -
  // plus three of its own: five seconds, 64 KB of template and 256 KB of output.
  // ?render&view=source answers the rendered HTML as text.
  //
  // A template error ends the request with PAD's own error report, which the playground
  // shows in the output frame like any other answer. The render page's own variables are
  // gone before the template runs, so all it sees is its data.

  set_time_limit ( 5 );

  $playSource = (string) ( $_POST ['source'] ?? '' );
  $playJson   = trim ( (string) ( $_POST ['data'] ?? '' ) );
  $playPhp    = (string) ( $_POST ['php'] ?? '' );
  $playMode   = ( ( $_POST ['mode'] ?? '' ) == 'php' ) ? 'php' : 'json';
  $playView   = ( ( $_GET ['view'] ?? '' ) == 'source' );
  $playEcho   = '';

  if ( strlen ( $playSource ) > 65536 )
    padError ( 'the template is longer than the playground takes: 64 KB' );

  // PHP code runs as a page's .php does, as written and with all of PHP's reach - the
  // application's guards keep it to this machine - but in a function of its own: the
  // variables it sets are collected afterwards and handed on like the keys of a JSON object,
  // and what it echoes stands before the template, as a page's echo does. Code without an
  // opening tag is PHP from its first line.

  if ( $playMode == 'php' ) {

    if ( strlen ( $playPhp ) > 65536 )
      padError ( 'the PHP code is longer than the playground takes: 64 KB' );

    $playRun = static function ( $playCode ) {
      eval ( $playCode );
      return get_defined_vars ();
    };

    $playLevel = ob_get_level ();
    ob_start ();

    try {
      $playData = $playRun ( str_starts_with ( ltrim ( $playPhp ), '<?' ) ? '?>' . $playPhp : $playPhp );
    } catch ( ParseError $playError ) {
      while ( ob_get_level () > $playLevel )
        ob_end_clean ();
      padError ( 'the PHP code does not parse: ' . $playError->getMessage () . ' on line ' . $playError->getLine () );
      $playData = [];
    }

    if ( ob_get_level () > $playLevel )
      $playEcho = (string) ob_get_clean ();

  } else {

    $playData = ( $playJson === '' ) ? [] : json_decode ( $playJson, TRUE );

    if ( ! is_array ( $playData ) or array_is_list ( $playData ) and $playData )
      padError ( 'the data is not a JSON object: ' . ( json_last_error () ? json_last_error_msg () : 'give it as { "name": value }' ) );

  }

  foreach ( $playData as $playKey => $playValue )
    if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/', $playKey ) and padValidStore ( $playKey ) and ! str_starts_with ( $playKey, 'play' ) )
      $GLOBALS [$playKey] = $playValue;

  $playSource = $playEcho . $playSource;

  unset ( $playJson, $playPhp, $playMode, $playEcho, $playRun, $playLevel, $playError, $playData, $playKey, $playValue );

  $playOutput = (string) padCode ( $playSource );

  if ( strlen ( $playOutput ) > 262144 )
    $playOutput = substr ( $playOutput, 0, 262144 ) . "\n... the output stops here: the playground shows 256 KB";

  // The source view shows the HTML exactly as the page would get it - the escapes of the pass
  // put back first, as exits/exits.php does - entities and all: an &lt; the template wrote
  // is shown as &lt;, not as the < a browser would make of it.

  $playShown = htmlspecialchars ( padUnprotect ( padUnescape ( $playOutput ) ), ENT_QUOTES, 'UTF-8', TRUE );

?>
