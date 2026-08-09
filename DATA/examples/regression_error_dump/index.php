<?php

  // Fetches the boom page - an undefined variable its .php reads - and asserts what the
  // 'dump' error action is supposed to do with it: a dump tree lands under DATA and the page finishes.

  $r    = padCurl ( $padHost . 'regression_error_dump/?boom&padInclude' );
  $code = $r ['result'];
  $body = $r ['data'];

  $dumped  = count ( glob ( DATA . "dumps/regression_error_dump/*" ) ?: [] ) > 0;
  $verdict = ( $code == 200 and str_contains ( $body, "after" ) and $dumped ) ? "yes" : "NO";

  $action = $padErrorAction;

?>