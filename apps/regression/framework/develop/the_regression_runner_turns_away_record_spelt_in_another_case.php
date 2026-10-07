<?php

  // On a filesystem that ignores case - macOS, Windows - ?Record reaches record.php with the
  // page name as it was written, so a guard that named 'record' let it run for any visitor,
  // and its preview hands out the link that writes. A request that is not this machine's
  // own reaches the read-only overviews and nothing else, whatever the spelling.

  $answer = '';

  foreach ( [ 'Record&suite=nosuch&name=x', 'RECORD&suite=nosuch&name=x', 'index' ] as $ask ) {
    $curl    = padCurl ( [ 'url' => $padHost . "regression/main/?$ask", 'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );
    $answer .= $curl ['result'] . ' ';
  }

?>
