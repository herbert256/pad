<?php

  // A request body is kept as its form fields with the secrets redacted, read back with
  // parse_str - which warns past max_input_vars: under the track mode a post of more fields
  // than that answered 500, and on the local error page the warning cut the report short.

  $fields = [];

  for ( $i = 0; $i < (int) ini_get ( 'max_input_vars' ) + 100; $i++ )
    $fields [] = "f$i=v";

  $curl = padCurl ( [ 'url' => $padHost . 'regression/info/?probe&padInclude', 'post' => implode ( '&', $fields ) . '&password=zzPostSecret' ] );

  $answer = $curl ['result'] . ' ' . ( str_contains ( $curl ['data'], 'TWO' ) ? 'rendered' : 'NOT RENDERED' );

?>
