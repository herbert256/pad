<?php

  // In the browser (padWasm - here as the runtime of a packed application says it, in
  // $_SERVER) a stream is one round: the producer is called once and the browser is asked
  // back after the interval, where on a server PAD calls it again until it answers FALSE.

  $_SERVER ['PAD_WASM'] = '1';

  $round = 0;

  padSse ( function () use ( &$round ) {

    $round++;

    if ( $round == 3 ) return FALSE;

    return [ 'round' => $round ];

  }, [ 'every' => 0.25, 'for' => 5, 'event' => 'stats' ] );

?>
