<?php

  // A wait of INF milliseconds is no wait padRetry can make: it is reported with the
  // other waits that are no number of 0 or more. Cast to an int for usleep, it raised PHP
  // 8.5's "the float INF is not representable as an int" after the first failed attempt.

  $answer = padRetry ( 2, function ( $attempt ) {
    if ( $attempt < 2 )
      throw new RuntimeException ( 'not yet' );
    return 'done';
  }, INF );

?>
