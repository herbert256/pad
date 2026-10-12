<?php

  // A login still checking its password while a second process removes the user writes
  // its login time to the users as they stand by then - the user stays removed and the
  // login is refused - where it wrote back the list it had read and the user was back.
  // The users are kept in a scratch directory for the test, removed at the end.

  $raceStore              = DATA . 'admin-test-' . getmypid () . '/';
  $GLOBALS ['adminStore'] = $raceStore;

  adminUserAdd ( 'alice', 'alice-password' );
  adminUserAdd ( 'bob',   'bob-password' );

  $raceCode = "define ( 'DATA', " . var_export ( DATA, TRUE ) . " ); \$GLOBALS ['adminStore'] = " . var_export ( $raceStore, TRUE )
            . "; include " . var_export ( APP . '_lib/auth.php', TRUE ) . "; usleep ( 50000 ); adminUserDelete ( 'bob' );";

  $raceChild = proc_open ( [ PHP_BINARY, '-r', $raceCode ], [], $racePipes );
  $raceStamp = adminUserCheck ( 'bob', 'bob-password' );

  proc_close ( $raceChild );

  $raceUsers = implode ( ', ', array_keys ( adminUsers () ) );
  $raceLogin = ( $raceStamp === '' ) ? 'refused' : 'let in';

  array_map ( 'unlink', glob ( $raceStore . '*' ) ?: [] );
  @rmdir ( $raceStore );

  unset ( $GLOBALS ['adminStore'] );

?>
