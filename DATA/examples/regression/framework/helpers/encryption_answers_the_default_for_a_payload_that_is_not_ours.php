<?php

  // Only the seal says a payload is ours: one changed in a single character - the last one
  // too, where base64 has bits that decode to nothing - cut short, lengthened, not base64,
  // empty, no text at all, or sealed with another key answers the default - NULL unless
  // one is given - and is no error, since a payload comes from outside.

  $payload = padEncrypt ( [ 'user' => 7 ] );
  $changed = $payload;
  $changed [20] = ( $changed [20] == 'A' ) ? 'B' : 'A';

  // The last character of 50 bytes in base64 carries two bits that decode to nothing: with
  // one of them flipped the bytes are the same, and still the payload is not the one made.

  $alphabet      = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
  $lastBits      = $payload;
  $lastBits [-1] = $alphabet [ strpos ( $alphabet, $payload [-1] ) ^ 1 ];

  $refused = [
    'changed'   => padDecrypt ( $changed, 'refused' ),
    'last bits' => padDecrypt ( $lastBits, 'refused' ),
    'cut'       => padDecrypt ( substr ( $payload, 0, -4 ), 'refused' ),
    'longer'    => padDecrypt ( $payload . 'AAAA', 'refused' ),
    'short'     => padDecrypt ( 'AAAA', 'refused' ),
    'spaces'    => padDecrypt ( substr ( $payload, 0, 10 ) . ' ' . substr ( $payload, 10 ), 'refused' ),
    'garbage'   => padDecrypt ( 'not base64!', 'refused' ),
    'empty'     => padDecrypt ( '', 'refused' ),
    'null'      => padDecrypt ( NULL, 'refused' ),
    'array'     => padDecrypt ( [ $payload ], 'refused' ),
    'default'   => padDecrypt ( 'AAAA' )
  ];

  $padAppKey = 'base64:' . base64_encode ( str_repeat ( 'k', 32 ) );

  $refused ['other key'] = padDecrypt ( $payload, 'refused' );

  $padAppKey = '';

  $refused ['own key']   = padDecrypt ( $payload, 'refused' );

  $refused = json_encode ( $refused );

?>
