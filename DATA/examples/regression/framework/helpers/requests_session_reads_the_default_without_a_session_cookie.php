<?php

  // A request without a session cookie has no session to read: every read answers the
  // default - an empty array for all of it - and none of them starts a session to find
  // that out. padOld has nothing either: no request before this one flashed input.

  $yes = fn ( $b ) => $b ? 'yes' : 'no';

  $s1 = padSession ( 'user.name', 'nobody' );
  $s2 = json_encode ( padSession () );
  $s3 = $yes ( padSessionHas ( 'user' ) );
  $s4 = padSessionPull ( 'user', 'nothing to take' );

  padSessionForget ( 'user' );

  $s5 = session_status () === PHP_SESSION_ACTIVE ? 'a session started' : 'no session started';
  $s6 = padOld ( 'email', 'no old input' ) . ' ' . json_encode ( padOld () );

?>
