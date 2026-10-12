<?php

  // A visitor's remember-me cookie, made up: its id goes to the application's finder before
  // the signature is checked, and a finder that pasted it into SQL ran what the visitor wrote.
  // Only a number or a plain token reaches the finder now.

  $asked = [];

  function rememberPlainFind ( $id ) { $GLOBALS ['asked'] [] = $id; return NULL; }

  $padAuthRemember = 'rememberPlainFind';

  foreach ( [ "1 OR 1=1", "x'; DROP TABLE users; --", 'ann@example.com', 42 ] as $forged ) {
    unset ( $_SESSION ['padAuth'] );
    padAuthStore ( 'checked', FALSE );
    padAuthStore ( 'now', NULL );
    $_COOKIE [ padAuthCookieName () ] = padCryptB64 ( json_encode ( [ $forged, time () + 3600 ] ) ) . '.forged';
    padUser ();
  }

  $asked = implode ( ', ', $asked );

?>
