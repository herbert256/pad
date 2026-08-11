<?php

  // A body that is no form: posted as a plain string with its own content type, read back
  // through php://input by the fixture.

  $curl = padCurl ( [ 'url' => $padGoExt . 'request/body&padInclude',
                      'post' => 'plain text payload',
                      'headers' => [ 'Content-Type' => 'text/plain' ] ] );

  $rawResult = $curl ['result'] . ' ' . padEscape ( trim ( $curl ['data'] ) );

?>
