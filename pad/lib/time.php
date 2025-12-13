<?php


  /**
   * Returns current timestamp with microseconds.
   *
   * @return string Timestamp in YmdHisu format.
   */
  function padTimeStamp () {

    $now = DateTime::createFromFormat('U.u', sprintf('%.6f', microtime(TRUE)));

    return $now->format('YmdHisu');

  }


  /**
   * Calculates duration in nanoseconds.
   *
   * @param float $start Start time (default: request start).
   * @param float $end   End time (default: now).
   *
   * @return int Duration in nanoseconds.
   */
  function padDuration ( $start = 0, $end = 0 ) {

    if ( ! $start ) $start = $_SERVER ['REQUEST_TIME_FLOAT'] ?? $GLOBALS ['padMicro'] ?? microtime ( true );
    if ( ! $end   ) $end   = microtime ( true );

    $duration = (int) ( ( $end - $start ) * 1000000000 );

    return $duration;

  }


  /**
   * Calculates high-resolution duration in nanoseconds.
   *
   * @param int $start Start hrtime (default: request start).
   * @param int $end   End hrtime (default: now).
   *
   * @return int Duration in nanoseconds.
   */
  function padDurationHR ( $start = 0, $end = 0 ) {

    if ( ! $start ) $start = $GLOBALS ['padHR'] ?? hrtime ( TRUE );
    if ( ! $end   ) $end   = hrtime ( TRUE );

    return $end - $start;

  }


?>
