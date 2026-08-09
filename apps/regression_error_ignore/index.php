<?php

  // Fetches the boom page - an undefined variable its .php reads - and asserts what the
  // 'ignore' error action is supposed to do with it: nothing reaches the visitor and the page finishes.

  $r    = padCurl ( $padHost . 'regression_error_ignore/?boom&padInclude' );
  $code = $r ['result'];
  $body = $r ['data'];

  $verdict = ( $code == 200 and str_contains ( $body, "before" )
             and str_contains ( $body, "after" )
             and ! str_contains ( $body, "Warning" ) ) ? "yes" : "NO";

  $action = $padErrorAction;

?>
