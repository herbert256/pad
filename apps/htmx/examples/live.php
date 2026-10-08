<?php

  // ?examples/live&stream is the stream: padSse sends a row of HTML every second, thirty in
  // all, as 'tick' events that htmx's sse extension swaps into the list (sse-swap="tick"),
  // then a 'done' event that closes the connection (sse-close="done"). The page itself -
  // asked without stream - is the list they come into.

  if ( padRequestHas ( 'stream' ) )
    padSse ( function ( $send ) {

      for ( $n = 1; $n <= 30; $n++ ) {

        $load = function_exists ( 'sys_getloadavg' ) ? number_format ( sys_getloadavg () [0], 2 ) : '-';

        $row = '<li class="tick rise"><span class="badge is-accent">#' . $n . '</span> '
             . '<code>' . date ( 'H:i:s' ) . '</code> load ' . htmlspecialchars ( $load )
             . ', ' . round ( memory_get_usage () / 1024 ) . ' kB in use</li>';

        if ( ! $send ( 'tick', $row ) )
          return;

        sleep ( 1 );

      }

      $send ( 'done', '' );

    }, [ 'retry' => 5000 ] );

?>
