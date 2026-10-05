<?php

  // A service that fails twice before it answers, as a busy remote one might: three
  // attempts, 10 milliseconds apart.

  $attempts = 0;

  $rate = padRetry ( 3, function ( $attempt ) use ( &$attempts ) {
    $attempts = $attempt;
    if ( $attempt < 3 )
      throw new RuntimeException ( 'timeout' );
    return 'EUR 1 = USD 1.17';
  }, 10 );

  // A part of the page that may fail fails alone: the page shows a fallback instead.

  $forecast = padRescue ( function () { throw new RuntimeException ( 'the weather service is down' ); },
                          fn ( $e ) => 'No forecast today - ' . $e->getMessage (),
                          FALSE );

?>
