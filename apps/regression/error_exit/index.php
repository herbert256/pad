<?php

  // Test fetches the boom page - an undefined variable its .php reads - and asserts what
  // the 'exit' error action is supposed to do with it: the request ends where it stood,
  // shipping nothing, as a 500 - it answered 200, which the check asserted. A plain load only offers the link, so a crawl raises nothing.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    $r    = padCurl ( $padHost . 'regression/error_exit/?boom&padInclude' );
    $code = $r ['result'];
    $body = $r ['data'];

    $verdict = ( $code == 500 and trim ( $body ) == "" ) ? "yes" : "NO";

  }

  $action = $padErrorAction;

?>