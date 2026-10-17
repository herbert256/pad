<?php

  // padAttempt with remember-me on a hash made with older settings, called without the
  // $rehash argument: the application keeps the old hash, and the cookie is signed with
  // that one - signed with a new hash the finder never returns, it never logged in again.

  $users = [ 4 => [ 'id' => 4, 'name' => 'Cid', 'password' => password_hash ( 'three', PASSWORD_BCRYPT, [ 'cost' => 4 ] ) ] ];

  function keptHashCaseFind ( $id ) { return $GLOBALS ['users'] [$id] ?? NULL; }

  $padAuthRemember = 'keptHashCaseFind';

  $in = padAttempt ( 'three', $users [4] ['password'], $users [4], TRUE ) ? 'in' : 'refused';

  unset ( $_SESSION ['padAuth'] );
  padAuthStore ( 'checked', FALSE );
  padAuthStore ( 'now', NULL );

  $later = padUser ( 'name' ) ?? 'guest';

  padLogout ();

?>
