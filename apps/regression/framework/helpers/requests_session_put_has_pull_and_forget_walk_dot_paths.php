<?php

  // Writing starts the session; the values are read back by dot path, an array of names
  // and values puts them all, a NULL is a value that is there, Pull reads and forgets, and
  // Forget takes one name or a list. A new id keeps the data.

  $yes = fn ( $b ) => $b ? 'yes' : 'no';

  $p1 = $yes ( padSessionPut ( 'cart.items', [ 'apple', 'pear' ] ) );
  $p2 = $yes ( padSessionPut ( [ 'theme' => 'dark', 'coupon' => NULL, 'cart.total' => 7 ] ) );

  $g1 = padSession ( 'cart.items.1' ) . ' ' . padSession ( 'cart.total' ) . ' ' . padSession ( 'theme' );
  $g2 = json_encode ( padSession ( 'cart.items.*' ) );
  $g3 = $yes ( padSessionHas ( 'coupon' ) ) . ' ' . $yes ( padSessionHas ( 'cart.items, theme' ) ) . ' ' . $yes ( padSessionHas ( 'cart.nope' ) );

  $u1 = padSessionPull ( 'theme' ) . ' ' . $yes ( padSessionHas ( 'theme' ) ) . ' ' . padSessionPull ( 'theme', 'gone' );

  padSessionForget ( 'cart.items, coupon' );

  $f1 = json_encode ( [ padSession ( 'cart' ), padSessionHas ( 'coupon' ), padSessionHas ( 'theme' ) ] );

  $padOldId = session_id ();
  $r1 = $yes ( padSessionRegenerate () ) . ' ' . ( session_id () !== $padOldId ? 'new id' : 'same id' ) . ' ' . padSession ( 'cart.total' );

?>
