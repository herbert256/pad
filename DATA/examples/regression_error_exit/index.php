<?php

  // Test fetches the boom page - an undefined variable its .php reads - and asserts what
  // the 'exit' error action is supposed to do with it: the request ends where it stood,
  // shipping nothing. A plain load only offers the link, so a crawl raises nothing.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    $r    = padCurl ( $padHost . 'regression_error_exit/?boom&padInclude' );
    $code = $r ['result'];
    $body = $r ['data'];

    $verdict = ( $code == 200 and trim ( $body ) == "" ) ? "yes" : "NO";

  }

  $action = $padErrorAction;

?>