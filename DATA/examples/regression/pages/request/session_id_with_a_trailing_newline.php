<?php

  // A session id from the browser with a newline after its eight letters and digits -
  // %0A, which PHP decodes - is no id PAD minted: the request goes on with a new one.

  $curl = padCurl ( [ 'url'     => $padGoExt . 'request/jar&padInclude',
                      'options' => [ 'COOKIE' => 'padSesID=abcdefgh%0A; jar=kept' ] ] );

  $sesNewline = $curl ['result'] . ' ' . padEscape ( trim ( $curl ['data'] ) );

?>
{!sesNewline}
