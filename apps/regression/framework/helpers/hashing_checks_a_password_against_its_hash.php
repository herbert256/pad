<?php

  // padHash is password_hash with PASSWORD_DEFAULT - a new salt every time - and
  // padHashCheck tells whether a password matches. A password that is no text, or a hash
  // that is none - a missing user's NULL - does not match, and is no error.

  $hash = padHash ( 'secret' );

  $hashShape = substr ( $hash, 0, 4 ) . ' ' . strlen ( $hash ) . ' ' . ( $hash !== padHash ( 'secret' ) ? 'salted' : 'unsalted' );

  $number  = padHash ( 12345 );
  $stringy = new class { function __toString () { return 'secret'; } };

  $hashCheck = json_encode ( [
    'right'      => padHashCheck ( 'secret', $hash ),
    'wrong'      => padHashCheck ( 'Secret', $hash ),
    'empty'      => padHashCheck ( '', $hash ),
    'number'     => padHashCheck ( '12345', $number ),
    'int'        => padHashCheck ( 12345, $number ),
    'stringable' => padHashCheck ( $stringy, $hash ),
    'null'       => padHashCheck ( NULL, $hash ),
    'array'      => padHashCheck ( [ 'secret' ], $hash ),
    'no hash'    => padHashCheck ( 'secret', NULL ),
    'blank'      => padHashCheck ( 'secret', '' ),
    'junk'       => padHashCheck ( 'secret', 'not a hash' )
  ] );

?>
