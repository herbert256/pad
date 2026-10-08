<?php

  // PAD calls the producer on an interval: an array is sent as the named event, NULL sends
  // nothing that round, FALSE ends the stream.

  $round = 0;

  padSse ( function () use ( &$round ) {

    $round++;

    if ( $round == 2 ) return NULL;
    if ( $round == 4 ) return FALSE;

    return [ 'round' => $round ];

  }, [ 'every' => 0.01, 'for' => 5, 'event' => 'stats' ] );

?>
