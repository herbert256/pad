<?php

  // A stream the producer writes itself runs as long as 'for' says, plus a while: the loop
  // form kept PHP's own time limit, and a producer of thirty events a second apart was
  // killed before its last one where that limit is timed on the wall clock.

  padSse ( function ( $send ) {

    $send ( 'limit', ini_get ( 'max_execution_time' ) );

  }, [ 'for' => 45 ] );

?>
