<?php

  // develop's tools act on a plain GET - ?clean rewrites the checkout's sources, ?build
  // wipes the suite results - so they answer this machine only: a request forwarded for
  // another address is refused by the guard. ?coverage without a word is the harmless one.

  $curl   = padCurl ( [ 'url' => $padHost . 'develop/?coverage', 'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
