<?php

  // INF attempts is no limit - the callback is called until it answers, as Laravel's retry
  // counts down from INF for ever - and '1e30' is as good as one. The attempts were cast to
  // an int, and INF raised PHP 8.5's "not representable as an int" warning.

  $answer = padRetry ( INF, function ( $attempt ) {
    if ( $attempt < 4 )
      throw new RuntimeException ( "attempt $attempt failed" );
    return "done on attempt $attempt";
  } );

  $text = padRetry ( '1e30', fn ( $attempt ) => "at once on $attempt" );

?>
