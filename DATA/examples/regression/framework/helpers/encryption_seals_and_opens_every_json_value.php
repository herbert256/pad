<?php

  // padEncrypt seals what JSON holds - text, numbers, booleans, NULL, arrays - into one
  // URL-safe string, a new nonce every time; padDecrypt opens it. An object comes back as
  // an array, and 1.0 stays a float.

  $sealed = [];

  foreach ( [ 'text', '', '0', 0, 1.0, 2.5, -7, TRUE, FALSE, NULL, [], [ 'a' => [ 1, 2 ], 'b' => NULL ],
              (object) [ 'x' => 1 ], 'Zoë ☃ & <b>' ] as $value ) {
    $payload   = padEncrypt ( $value );
    $sealed [] = var_export ( padDecrypt ( $payload, 'refused' ), TRUE )
               . ( preg_match ( '/^[A-Za-z0-9_-]+$/', $payload ) ? '' : ' NOT URL-SAFE' );
  }

  $sealed = str_replace ( "\n", '', implode ( ' | ', $sealed ) );
  $fresh  = ( padEncrypt ( 'same' ) !== padEncrypt ( 'same' ) ) ? 'a new nonce each time' : 'the same twice';

?>
