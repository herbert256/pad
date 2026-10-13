<?php

  // A remember-me cookie whose payload is a JSON object, not the list of id and expiry
  // the engine writes, is dropped as any cookie that does not hold - a visitor's cookie is
  // never an error - where its two keys passed for the two entries and reading entry 1
  // ended the request.

  function rememberListCaseFind ( $id ) { return [ 'id' => $id, 'name' => 'Dee', 'password' => 'x' ]; }

  $padAuthRemember = 'rememberListCaseFind';

  $who = function ( $payload ) {
    unset ( $_SESSION ['padAuth'] );
    padAuthStore ( 'checked', FALSE );
    padAuthStore ( 'now', NULL );
    $_COOKIE [ padAuthCookieName () ] = padCryptB64 ( json_encode ( $payload ) ) . '.x';
    return padUser ( 'name' ) ?? 'guest';
  };

  $object  = $who ( [ 'a' => 1, 'b' => 2 ] );
  $shifted = $who ( [ '1' => 9999999999, 'x' => 1 ] );

?>
