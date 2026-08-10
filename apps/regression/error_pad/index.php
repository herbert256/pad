<?php

  // Test fetches the boom page - an undefined variable its .php reads - and asserts what
  // the 'pad' error action is supposed to do with it: a 500 whose page carries the
  // message. A plain load only offers the link, so a crawl of this page raises nothing.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    $r    = padCurl ( $padHost . 'regression/error_pad/?boom&padInclude' );
    $code = $r ['result'];
    $body = $r ['data'];

    $verdict = ( $code == 500 and str_contains ( $body, "Undefined variable" )
               and str_contains ( $body, "neverSetAnywhere" ) ) ? "yes" : "NO";

  }

  $action = $padErrorAction;

?>