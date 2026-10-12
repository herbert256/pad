<?php

  // Applications a/b, a_b and a-b: their remember-me cookies had one name, and a visit to one
  // dropped the cookie of the other - its signature, made for another application, failed.

  $cookieApp   = $GLOBALS ['padApp'];
  $cookieNames = [];

  foreach ( [ 'a/b', 'a_b', 'a-b', 'shop' ] as $cookieOne ) {
    $GLOBALS ['padApp'] = $cookieOne;
    $cookieNames []     = padAuthCookieName ();
  }

  $GLOBALS ['padApp'] = $cookieApp;

  $cookieNames = implode ( ' ', $cookieNames ) . ( count ( array_unique ( $cookieNames ) ) == 4 ? ' - all apart' : ' - shared' );

?>
