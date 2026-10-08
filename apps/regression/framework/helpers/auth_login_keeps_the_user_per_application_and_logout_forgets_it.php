<?php

  // Before: a guest. padLogin keeps the row under this application in the session - less
  // its password - with a new session id; padUser reads it whole, by field and by dot path.
  // padLogout forgets it, with the application's session names, and gives a new id again.

  $yes = fn ( $b ) => $b ? 'yes' : 'no';

  $before = $yes ( padAuthCheck () ) . ' ' . json_encode ( [ padUser (), padUserId () ] );

  padSessionStart ();
  $firstId = session_id ();

  $cartKept = NULL;
  $padSessionVars = [ 'cartKept' ];
  $cartKept = 'three apples';

  $in = $yes ( padLogin ( [ 'id' => '42', 'name' => 'Ann', 'address' => [ 'city' => 'Gouda' ], 'password' => 'hash' ] ) )
      . ' ' . $yes ( session_id () !== $firstId )
      . ' ' . $yes ( padAuthCheck () )
      . ' ' . padUserId () . ' ' . padUser ( 'name' ) . ' ' . padUser ( 'address.city' )
      . ' ' . json_encode ( padUser ( 'password' ) )
      . ' ' . json_encode ( array_keys ( $_SESSION ['padAuth'] ) );

  $secondId = session_id ();

  $out = $yes ( padLogout () )
       . ' ' . $yes ( session_id () !== $secondId )
       . ' ' . $yes ( padAuthCheck () )
       . ' ' . json_encode ( padUser () )
       . ' ' . json_encode ( $cartKept );

?>
