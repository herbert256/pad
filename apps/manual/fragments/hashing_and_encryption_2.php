<?php

  $payload = padEncrypt ( [ 'order' => 1042, 'step' => 'payment' ] );

  $length = strlen ( $payload );
  $back   = padDecrypt ( $payload );
  $forged = padDecrypt ( substr ( $payload, 0, -1 ) . 'x', 'refused' );

?>
