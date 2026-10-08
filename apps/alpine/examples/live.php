<?php

  // ?examples/live&stream is the stream: padSse calls the producer every second for half a
  // minute and sends what it answers as a 'stats' event - JSON - then a 'done' event; the
  // page itself, asked without stream, is the board the numbers come into.

  if ( padRequestHas ( 'stream' ) ) {

    $round = 0;

    padSse ( function ( $send ) use ( &$round ) {

      $round++;

      if ( $round > 30 ) {
        $send ( 'done', 'done' );
        return FALSE;
      }

      $load = function_exists ( 'sys_getloadavg' ) ? round ( sys_getloadavg () [0], 2 ) : 0;

      return [ 'round' => $round, 'time' => date ( 'H:i:s' ), 'load' => $load,
               'memory' => round ( memory_get_usage () / 1024 ) ];

    }, [ 'every' => 1, 'for' => 35, 'event' => 'stats', 'retry' => 5000 ] );

  }

?>
