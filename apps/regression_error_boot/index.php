<?php

  // Fetches the boom page - an undefined variable its .php reads - and asserts what the
  // 'boot' error action is supposed to do with it: a 500 whose body is the machine-readable
  // JSON dump, message, file and line included.

  $r    = padCurl ( $padHost . 'regression_error_boot/?boom&padInclude' );
  $code = $r ['result'];
  $body = $r ['data'];
  $json = json_decode ( $body, TRUE );

  $verdict = ( $code == 500
               and is_array ( $json )
               and str_contains ( $json ['error'] ?? '', 'neverSetAnywhere' )
               and str_contains ( $json ['file']  ?? '', 'boom.php' )
               and isset ( $json ['pad'] ) ) ? 'yes' : 'NO';

  $action = $padErrorAction;

?>
