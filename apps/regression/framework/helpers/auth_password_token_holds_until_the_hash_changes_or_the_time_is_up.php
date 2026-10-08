<?php

  // The token is bound to the address (in any case), the password hash it was made for and
  // its expiry: a new password - a new hash - ends it, as does another address, a changed
  // character, and the hour passing.

  $yes  = fn ( $b ) => $b ? 'yes' : 'no';
  $hash = padHash ( 'old password' );

  padNowFreeze ( '2026-10-08 12:00:00' );

  $token = padPasswordToken ( 'Ann@Example.com', $hash );

  $shape = $yes ( (bool) preg_match ( '/^[0-9a-z]+\.[A-Za-z0-9_-]{43}$/', $token ) );

  $now = $yes ( padPasswordTokenCheck ( $token, 'ann@example.com ', $hash ) )
       . ' ' . $yes ( padPasswordTokenCheck ( $token, 'bob@example.com', $hash ) )
       . ' ' . $yes ( padPasswordTokenCheck ( $token, 'ann@example.com', padHash ( 'new password' ) ) )
       . ' ' . $yes ( padPasswordTokenCheck ( substr ( $token, 0, -1 ) . ( substr ( $token, -1 ) == 'A' ? 'B' : 'A' ), 'ann@example.com', $hash ) )
       . ' ' . $yes ( padPasswordTokenCheck ( [ $token ], 'ann@example.com', $hash ) );

  padNowFreeze ( '2026-10-08 12:59:59' );
  $late = $yes ( padPasswordTokenCheck ( $token, 'ann@example.com', $hash ) );

  padNowFreeze ( '2026-10-08 13:00:01' );
  $later = $yes ( padPasswordTokenCheck ( $token, 'ann@example.com', $hash ) );

  padNowFreeze ( NULL );

?>
