<?php

  // Ten requests at once, each counting one hit on the same rate limit: every hit is
  // counted. A hit reads the count, adds one and writes it; without the lock two requests
  // read the same count and write the same next one, and a hit is lost.

  padRateLimitClear ( 'burst' );

  $multi   = curl_multi_init ();
  $handles = [];

  for ( $one = 1; $one <= 10; $one++ ) {
    $handle = curl_init ( $padHost . "regression/env/?hit&n=$one&padInclude" );
    curl_setopt ( $handle, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt ( $handle, CURLOPT_TIMEOUT, 30 );
    curl_multi_add_handle ( $multi, $handle );
    $handles [] = $handle;
  }

  do {
    $status = curl_multi_exec ( $multi, $running );
    if ( $running )
      curl_multi_select ( $multi, 1.0 );
  } while ( $running and $status == CURLM_OK );

  $answered = 0;

  foreach ( $handles as $handle ) {
    if ( trim ( (string) curl_multi_getcontent ( $handle ) ) === 'counted' )
      $answered++;
    curl_multi_remove_handle ( $multi, $handle );
  }

  $counted = 1000 - padRateLimitRemaining ( 'burst', 1000 );

?>
