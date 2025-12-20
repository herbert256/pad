<?php

  function padTimeStamp () {

    $now = DateTime::createFromFormat('U.u', sprintf('%.6f', microtime(TRUE)));

    return $now->format('YmdHisu');

  }

  function padDuration ( $start = 0, $end = 0 ) {

    if ( ! $start ) $start = $_SERVER ['REQUEST_TIME_FLOAT'] ?? $GLOBALS ['padMicro'] ?? microtime ( true );
    if ( ! $end   ) $end   = microtime ( true );

    $duration = (int) ( ( $end - $start ) * 1000000000 );

    return $duration;

  }

  function padDurationHR ( $start = 0, $end = 0 ) {

    if ( ! $start ) $start = $GLOBALS ['padHR'] ?? hrtime ( TRUE );
    if ( ! $end   ) $end   = hrtime ( TRUE );

    return $end - $start;

  }

?>
