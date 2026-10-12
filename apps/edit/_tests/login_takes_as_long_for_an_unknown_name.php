<?php

  // A name that is no user is checked against the engine's stand-in hash, of the cost of a
  // real one - one check, as for a user - where a stand-in was made first and then checked,
  // and an unknown name took twice the time of a known one. Every web request is a process
  // of its own, so the first unknown name of this one is timed - beside a known one, with
  // room for the noise of a busy machine.

  $timeBase = DATA . 'edit-test-time-' . getmypid () . '/';

  editRemoveTree ( $timeBase );
  editUserAdd ( $timeBase, 'anna', 'first-password' );

  $timeOf = function ( $name ) use ( $timeBase ) {
    $start = hrtime ( TRUE );
    editUserCheck ( $timeBase, $name, 'not-the-password' );
    return hrtime ( TRUE ) - $start;
  };

  $timeUnknown = $timeOf ( 'nobody' );
  $timeKnown   = $timeOf ( 'anna' );
  $timeSame    = ( $timeUnknown / max ( 1, $timeKnown ) < 1.5 ) ? 'about the same time' : 'longer';

  editRemoveTree ( $timeBase );

?>
