<?php

  // Five login attempts a minute. A page of your own keys it on the visitor -
  // 'login:' . $_SERVER ['REMOTE_ADDR'] - this example on a key of its own, so every
  // reader of this page starts from nothing.

  $key   = 'login:' . padRandomString ( 8 );
  $tries = [];

  for ( $try = 1; $try <= 7; $try++ )
    $tries [] = [ 'try' => $try, 'answer' => padRateLimit ( $key, 5, 60 ) ? 'allowed' : 'too many' ];

  $left    = padRateLimitRemaining ( $key, 5 );
  $minutes = ceil ( padRateLimitAvailableIn ( $key ) / 60 );

  padRateLimitClear ( $key );

?>
