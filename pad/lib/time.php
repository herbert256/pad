<?php

  function padTimeStamp () {

    $now = DateTime::createFromFormat('U.u', sprintf('%.6f', microtime(TRUE)));

    return $now->format('YmdHisu');

  }

  function padDuration ( $start = 0, $end = 0 ) {

    global $padMicro;

    if ( ! $start ) $start = $_SERVER ['REQUEST_TIME_FLOAT'] ?? $padMicro ?? microtime ( true );
    if ( ! $end   ) $end   = microtime ( true );

    $duration = (int) ( ( $end - $start ) * 1000000000 );

    return $duration;

  }

  function padDurationHR ( $start = 0, $end = 0 ) {

    global $padHR;

    if ( ! $start ) $start = $padHR ?? hrtime ( TRUE );
    if ( ! $end   ) $end   = hrtime ( TRUE );

    return $end - $start;

  }

?>