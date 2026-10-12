<?php

  // A name that is no user is checked against a stand-in hash of the cost a real one has -
  // one check, as for a known name - where a stand-in was made first and then checked, and
  // an unknown name took twice as long: which names exist could be read from the time.
  // Every web request is a process of its own, so the first unknown name of this one is
  // timed - beside a known one, with room for the noise of a busy machine.

  $timeStore              = DATA . 'admin-test-' . getmypid () . '/';
  $GLOBALS ['adminStore'] = $timeStore;

  adminUserAdd ( 'alice', 'alice-password' );

  $timeOf = function ( $name ) {
    $start = hrtime ( TRUE );
    adminUserCheck ( $name, 'not-the-password' );
    return hrtime ( TRUE ) - $start;
  };

  $timeUnknown = $timeOf ( 'nobody' );
  $timeKnown   = $timeOf ( 'alice' );
  $timeSame    = ( $timeUnknown / max ( 1, $timeKnown ) < 1.5 ) ? 'about the same time' : 'longer';

  array_map ( 'unlink', glob ( $timeStore . '*' ) ?: [] );
  @rmdir ( $timeStore );

  unset ( $GLOBALS ['adminStore'] );

?>
