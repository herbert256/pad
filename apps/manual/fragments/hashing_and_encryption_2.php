<?php

  $payload = padEncrypt ( [ 'order' => 1042,
                            'step'  => 'payment' ] );

  $length  = strlen ( $payload );
  $back    = padDecrypt ( $payload );
  $changed = substr ( $payload, 0, -1 ) . 'x';
  $forged  = padDecrypt ( $changed, 'refused' );

?>
