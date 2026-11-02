<?php

  // Renders the template Live preview posts, with the data posted beside it: a text in any
  // format PAD reads data in - JSON, YAML, XML, CSV ... - its type recognised as {pad:data}
  // recognises it, each top-level key a variable of the template, as a page's .php would set
  // it - or, with mode=php, PHP code that is such a .php. The template runs in a nested pass
  // of this request (padCode) under the application's limits - no PHP functions, no request
  // values - plus three of its own: five seconds, 64 KB of template and of data, and 256 KB
  // of output.
  // ?render&view=source answers the rendered HTML as text.
  //
  // A template error ends the request with PAD's own error report, which Live preview
  // shows in the output frame like any other answer. The render page's own variables are
  // gone before the template runs, so all it sees is its data.

  set_time_limit ( 5 );

  $playSource = (string) ( $_POST ['source'] ?? '' );
  $playText   = (string) ( $_POST ['data'] ?? '' );
  $playPhp    = (string) ( $_POST ['php'] ?? '' );
  $playMode   = ( ( $_POST ['mode'] ?? '' ) == 'php' ) ? 'php' : 'data';
  $playView   = ( ( $_GET ['view'] ?? '' ) == 'source' );
  $playEcho   = '';

  if ( strlen ( $playSource ) > 65536 )
    padError ( 'the template is longer than Live preview takes: 64 KB' );

  if ( strlen ( $playText ) > 65536 or strlen ( $playPhp ) > 65536 )
    padError ( 'the data is longer than Live preview takes: 64 KB' );

  // PHP code runs as a page's .php does, as written and with all of PHP's reach - the
  // application's guards keep it to this machine - but in a function of its own: the
  // variables it sets are collected afterwards and handed on like the keys of a record,
  // and what it echoes stands before the template, as a page's echo does. Code without an
  // opening tag is PHP from its first line.

  if ( $playMode == 'php' ) {

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

    $playData = liveData ( $playText );

  }

  foreach ( $playData as $playKey => $playValue )
    if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/', $playKey ) and padValidStore ( $playKey ) and ! str_starts_with ( $playKey, 'play' ) )
      $GLOBALS [$playKey] = $playValue;

  $playSource = $playEcho . $playSource;

  unset ( $playText, $playPhp, $playMode, $playEcho, $playRun, $playRead, $playLevel, $playError, $playData, $playKey, $playValue );

  $playOutput = (string) padCode ( $playSource );

  if ( strlen ( $playOutput ) > 262144 )
    $playOutput = substr ( $playOutput, 0, 262144 ) . "\n... the output stops here: Live preview shows 256 KB";

  // The source view shows the HTML exactly as the page would get it - the escapes of the pass
  // put back first, as exits/exits.php does - entities and all: an &lt; the template wrote
  // is shown as &lt;, not as the < a browser would make of it.

  $playShown = htmlspecialchars ( padUnprotect ( padUnescape ( $playOutput ) ), ENT_QUOTES, 'UTF-8', TRUE );

?>
