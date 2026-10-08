<?php

  // A stream the producer writes itself: the retry first, then three events - a named one
  // with an id and JSON data, one whose text runs over two lines, and a plain message.

  padSse ( function ( $send ) {

    $send ( 'tick', [ 'n' => 1, 'word' => 'één' ], 'a1' );
    $send ( 'note', "two\nlines" );
    $send ( 'message', 'plain' );

  }, [ 'retry' => 2500 ] );

?>
