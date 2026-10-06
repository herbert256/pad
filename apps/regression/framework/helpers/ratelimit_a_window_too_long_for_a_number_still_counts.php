<?php

  // A window longer than a number of seconds can hold - 1e20 - is as good as for ever, and
  // its hits are counted. The seconds were cast to a whole number, which PHP 8.5 reports as
  // a float it cannot represent, and the request ended; past that, the end of the window,
  // written as a float, was never read back and no hit was ever counted.

  padRateLimitClear ( 'fw-rate-long' );

  $hits = [];

  for ( $hit = 1; $hit <= 3; $hit++ )
    $hits [] = padRateLimit ( 'fw-rate-long', 2, 1e20 );

  $hits = json_encode ( $hits );
  $left = padRateLimitRemaining ( 'fw-rate-long', 2 );
  $far  = padRateLimitAvailableIn ( 'fw-rate-long' ) > 100 * 365 * 86400 ? 'far ahead' : 'soon';

  padRateLimitClear ( 'fw-rate-long' );

?>
