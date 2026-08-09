<?php

  // Test fetches the boom page - an undefined variable its .php reads - and asserts what
  // the 'stop' error action is supposed to do with it: a 500 with nothing in it. A plain
  // load only offers the link, so a crawl of this page raises nothing.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    $r    = padCurl ( $padHost . 'regression_error_stop/?boom&padInclude' );
    $code = $r ['result'];
    $body = $r ['data'];

    $verdict = ( $code == 500 and trim ( $body ) == "" ) ? "yes" : "NO";

  }

  $action = $padErrorAction;

?>