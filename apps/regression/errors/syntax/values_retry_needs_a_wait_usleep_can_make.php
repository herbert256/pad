<?php

  // A wait too long for usleep - 1e300 milliseconds, past any whole number of microseconds
  // PHP has - is reported with the other waits it cannot make. Finite, it passed the check
  // and its cast to an int raised PHP 8.5's "not representable as an int" after the first
  // failed attempt.

  $answer = padRetry ( 2, function ( $attempt ) {
    if ( $attempt < 2 )
      throw new RuntimeException ( 'not yet' );
    return 'done';
  }, 1e300 );

?>
