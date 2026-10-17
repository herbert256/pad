<?php

  // A small status board. The component asks for it every few seconds with padFormat=json;
  // padCacheIncrement keeps a count of the asks of every visitor together, under
  // DATA/cache/app/react/ - under a lock, so asks that come at once are each counted. The
  // rest is what the server knows at that moment.

  $hits = (int) padCacheIncrement ( 'liveHits', 1, 86400 );

  $load = function_exists ( 'sys_getloadavg' ) ? sys_getloadavg () : FALSE;

  $status = [ 'time'   => date ( 'H:i:s' ),
              'date'   => date ( 'l j F Y' ),
              'hits'   => $hits,
              'php'    => PHP_VERSION,
              'memory' => round ( memory_get_peak_usage () / 1048576, 1 ),
              'load'   => $load ? round ( $load [0], 2 ) : NULL,
              'pid'    => getmypid () ];

  $settings  = [ 'every' => 2 ];
  $padExpose = [ 'status' ];

?>
