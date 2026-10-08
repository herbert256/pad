<?php

  // A wrong password, and no user at all, are FALSE and log nobody in; the right one logs
  // the row in. A hash made with older settings - bcrypt at cost 4 - comes back renewed in
  // the fifth argument, one made with today's settings does not.

  $yes  = fn ( $b ) => $b ? 'yes' : 'no';
  $row  = [ 'id' => 5, 'name' => 'Cy' ];
  $old  = password_hash ( 'right', PASSWORD_BCRYPT, [ 'cost' => 4 ] );
  $now  = padHash ( 'right' );

  $wrong   = $yes ( padAttempt ( 'wrong', $now, $row ) ) . ' ' . $yes ( padAuthCheck () );
  $nobody  = $yes ( padAttempt ( 'right', NULL, NULL ) ) . ' ' . $yes ( padAuthCheck () );
  $current = $yes ( padAttempt ( 'right', $now, $row, FALSE, $new1 ) ) . ' ' . padUser ( 'name' ) . ' ' . json_encode ( $new1 );
  $renewed = $yes ( padAttempt ( 'right', $old, $row, FALSE, $new2 ) ) . ' '
           . $yes ( is_string ( $new2 ) and $new2 !== $old and padHashCheck ( 'right', $new2 ) and ! padHashNeedsRehash ( $new2 ) );

?>
