<?php

  // A value with a byte that is no UTF-8 is kept in a JSON answer, the byte as U+FFFD - as
  // the HTML page shows it - where the value was null, with no error even under the strict
  // check: data a client cannot tell from a real null.

  $latinJson = padCurl ( $padGoExt . 'format/latin&padFormat=json' );

  echo $latinJson ['result'], ' ', json_encode ( json_decode ( $latinJson ['data'] ) );

?>
