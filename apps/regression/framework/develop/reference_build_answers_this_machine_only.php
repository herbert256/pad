<?php

  // The reference build empties DATA/reference - files git keeps - and crawls every page
  // again: a visitor is turned away before it starts. Asked without its go, so that on a
  // server where it is not turned away it still does nothing.

  $curl   = padCurl ( [ 'url' => $padHost . 'reference/?build', 'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
