<?php

  padRateLimitClear ( 'fw-rate' );

  $hits = [];

  for ( $hit = 1; $hit <= 4; $hit++ )
    $hits [] = padRateLimit ( 'fw-rate', 3, 60 );

  $hits      = json_encode ( $hits );
  $remaining = padRateLimitRemaining ( 'fw-rate', 3 );
  $more      = padRateLimitRemaining ( 'fw-rate', 10 );
  $in        = padRateLimitAvailableIn ( 'fw-rate' );
  $within    = ( $in >= 59 and $in <= 60 ) ? 'yes' : 'no';

  $cleared = json_encode ( [ padRateLimitClear ( 'fw-rate' ), padRateLimitRemaining ( 'fw-rate', 3 ),
                             padRateLimitAvailableIn ( 'fw-rate' ) ] );

  padRateLimitClear ( 'fw-rate-zero' );

  $zero = json_encode ( padRateLimit ( 'fw-rate-zero', 0 ) );
  $text = json_encode ( [ padRateLimit ( 'fw-rate-zero', '1', '60' ), padRateLimitRemaining ( 'fw-rate-zero', '1' ) ] );

  // A window of one second: over the limit, and allowed again once it ended.

  padRateLimitClear ( 'fw-rate-short' );

  $short = [ padRateLimit ( 'fw-rate-short', 1, 1 ), padRateLimit ( 'fw-rate-short', 1, 1 ) ];

  usleep ( 1100000 );

  $short [] = padRateLimitAvailableIn ( 'fw-rate-short' );
  $short [] = padRateLimit ( 'fw-rate-short', 1, 1 );
  $short    = json_encode ( $short );

  padRateLimitClear ( 'fw-rate-short' );

?>
