<?php

  // Test fetches the boom page - two undefined variables its .php reads - and asserts what
  // the 'dump' error action is supposed to do with them: a full dump tree per error lands
  // under DATA and the page finishes. A plain load only offers the link, so a crawl raises
  // nothing.
  //
  // The dumps are the boom request's own: a report directory is named after the request
  // id, which the response carries in its PAD header as session-request, and a full one
  // holds a stack.html. Only the first error of a request used to get one.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    $r    = padCurl ( $padHost . 'regression/error_dump/?boom&padInclude' );
    $code = $r ['result'];
    $body = $r ['data'];
    $req  = substr ( strrchr ( $r ['headers'] ['PAD'] ?? '', '-' ), 1 );

    $dumped  = $req ? count ( glob ( DATA . "dumps/regression/error_dump/boom/$req-*/stack.html" ) ?: [] ) : 0;
    $verdict = ( $code == 200 and str_contains ( $body, "after" ) and $dumped == 2 ) ? "yes" : "NO";

  }

  $action = $padErrorAction;

?>
