<?php

  // The playground runs the template it is posted, on this machine - and a page on another
  // site can make this machine's browser post one, which comes from loopback like any
  // local request. A post that does not carry the token of the playground's own form is
  // refused before anything runs.

  $curl   = padCurl ( [ 'url' => $padHost . 'playground/?render', 'post' => [ 'source' => 'ran', 'data' => '{}' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
