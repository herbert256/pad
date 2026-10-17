<?php

  // In the browser the loop form ends at the first event sent a tenth of a second after the
  // stream began, once it is written: the burst sent at once goes out whole, the rest waits
  // for the reconnect the retry asks for - a second, as nothing else was said.

  $_SERVER ['PAD_WASM'] = '1';

  padSse ( function ( $send ) {

    $send ( 'word', 'one' );
    $send ( 'word', 'two' );

    usleep ( 200000 );

    $send ( 'word', 'three' );
    $send ( 'word', 'four' );

  } );

?>
