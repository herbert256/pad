<?php

  // padLogin with remember sets a signed cookie of the id and an expiry. A later request
  // without the login - played here by forgetting the session's login and this request's
  // memory of it - finds the row again through $padAuthRemember and is logged in. A new
  // password, a cookie changed by a character and a cookie past its time are not, and the
  // cookie is dropped.

  $yes   = fn ( $b ) => $b ? 'yes' : 'no';
  $users = [ 9 => [ 'id' => 9, 'name' => 'Dee', 'password' => padHash ( 'one' ) ] ];

  function rememberCaseFind ( $id ) { return $GLOBALS ['users'] [$id] ?? NULL; }

  $padAuthRemember = 'rememberCaseFind';

  $again = function ( $cookie ) {
    unset ( $_SESSION ['padAuth'] );
    padAuthStore ( 'checked', FALSE );
    padAuthStore ( 'now', NULL );
    $_COOKIE [ padAuthCookieName () ] = $cookie;
    return padUser ( 'name' ) ?? 'guest';
  };

  padNowFreeze ( '2026-10-08 12:00:00' );

  padLogin ( $users [9], TRUE );

  $cookie = $_COOKIE [ padAuthCookieName () ] ?? '';
  $name   = padAuthCookieName ();

  $same     = $again ( $cookie );
  $tampered = $again ( substr ( $cookie, 0, -1 ) . ( substr ( $cookie, -1 ) == 'A' ? 'B' : 'A' ) );
  $dropped  = $yes ( ! isset ( $_COOKIE [ padAuthCookieName () ] ) );

  padNowFreeze ( '2026-11-08 12:00:01' );
  $expired  = $again ( $cookie );

  padNowFreeze ( '2026-10-09 12:00:00' );
  $nextDay  = $again ( $cookie );

  $users [9] ['password'] = padHash ( 'two' );
  $changed  = $again ( $cookie );

  padNowFreeze ( NULL );

?>
