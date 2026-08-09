<?php

  // Test fetches the boom page - an undefined variable its .php reads - and asserts what
  // the 'log' error action is supposed to do with it: the error goes to the server log and
  // the page finishes. A plain load only offers the link, so a crawl raises nothing.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    $r    = padCurl ( $padHost . 'regression_error_log/?boom&padInclude' );
    $code = $r ['result'];
    $body = $r ['data'];

    $verdict = ( $code == 200 and str_contains ( $body, "before" )
               and str_contains ( $body, "after" )
               and ! str_contains ( $body, "Warning" ) ) ? "yes" : "NO";

  }

  $action = $padErrorAction;

?>