<?php

  // Fetches the boom page - an undefined variable its .php reads - and asserts what the
  // 'php' error action is supposed to do with it: PHP reports the warning itself and the page finishes.

  $r    = padCurl ( $padHost . 'regression_error_php/?boom&padInclude' );
  $code = $r ['result'];
  $body = $r ['data'];

  $verdict = ( $code == 200 and str_contains ( $body, "Warning" )
             and str_contains ( $body, "after" ) ) ? "yes" : "NO";

  $action = $padErrorAction;

?>
