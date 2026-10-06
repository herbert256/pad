<?php

  // Renders the template the playground posts, with the JSON object posted beside it as its
  // data: each top-level key a variable of the template, as a page's .php would set it. The
  // template runs in a nested pass of this request (padCode) under the application's limits -
  // no PHP functions, no request values - plus three of its own: five seconds, 64 KB of
  // template and 256 KB of output. ?render&view=source answers the rendered HTML as text.
  //
  // A template error ends the request with PAD's own error report, which the playground
  // shows in the output frame like any other answer. The render page's own variables are
  // gone before the template runs, so all it sees is its data.

  set_time_limit ( 5 );

  $playSource = (string) ( $_POST ['source'] ?? '' );
  $playJson   = trim ( (string) ( $_POST ['data'] ?? '' ) );
  $playView   = ( ( $_GET ['view'] ?? '' ) == 'source' );

  if ( strlen ( $playSource ) > 65536 )
    padError ( 'the template is longer than the playground takes: 64 KB' );

  $playData = ( $playJson === '' ) ? [] : json_decode ( $playJson, TRUE );

  if ( ! is_array ( $playData ) or array_is_list ( $playData ) and $playData )
    padError ( 'the data is not a JSON object: ' . ( json_last_error () ? json_last_error_msg () : 'give it as { "name": value }' ) );

  foreach ( $playData as $playKey => $playValue )
    if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/', $playKey ) and padValidStore ( $playKey ) and ! str_starts_with ( $playKey, 'play' ) )
      $GLOBALS [$playKey] = $playValue;

  unset ( $playJson, $playData, $playKey, $playValue );

  $playOutput = (string) padCode ( $playSource );

  if ( strlen ( $playOutput ) > 262144 )
    $playOutput = substr ( $playOutput, 0, 262144 ) . "\n... the output stops here: the playground shows 256 KB";

  // The source view shows the HTML exactly as the page would get it - the escapes of the pass
  // put back first, as exits/exits.php does - entities and all: an &lt; the template wrote
  // is shown as &lt;, not as the < a browser would make of it.

  $playShown = htmlspecialchars ( padUnprotect ( padUnescape ( $playOutput ) ), ENT_QUOTES, 'UTF-8', TRUE );

?>
