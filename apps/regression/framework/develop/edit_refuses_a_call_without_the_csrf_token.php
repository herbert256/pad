<?php

  // Every call of the editor is a post that carries the session's CSRF token.

  $curl   = padCurl ( [ 'url' => $padHost . 'edit/?api&action=apps&padFormat=json', 'post' => [ 'app' => 'demo' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
