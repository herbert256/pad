<?php

  // padAttempt with remember-me on a hash made with older settings: the application stores
  // the new hash it hands back, as the documentation says, and the cookie still logs the
  // user in - it is signed with the new hash, where it was signed with the old one and the
  // update made it invalid at once.

  $users = [ 4 => [ 'id' => 4, 'name' => 'Cid', 'password' => password_hash ( 'three', PASSWORD_BCRYPT, [ 'cost' => 4 ] ) ] ];

  function rehashCaseFind ( $id ) { return $GLOBALS ['users'] [$id] ?? NULL; }

  $padAuthRemember = 'rehashCaseFind';

  $in = padAttempt ( 'three', $users [4] ['password'], $users [4], TRUE, $new ) ? 'in' : 'refused';

  if ( $new )
    $users [4] ['password'] = $new;

  unset ( $_SESSION ['padAuth'] );
  padAuthStore ( 'checked', FALSE );
  padAuthStore ( 'now', NULL );

  $later = padUser ( 'name' ) ?? 'guest';

  padLogout ();

?>
