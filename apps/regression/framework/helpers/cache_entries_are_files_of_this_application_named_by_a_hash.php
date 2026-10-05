<?php

  padCachePut ( 'fw-files-key', 'kept' );

  $dir    = DATA . 'cache/app/regression/framework/';
  $hashed = is_file ( $dir . md5 ( "cache\nfw-files-key" ) . '.cache' ) ? 'yes' : 'no';
  $named  = glob ( $dir . '*fw-files-key*' ) ? 'yes' : 'no';

  // A cache key and a rate limit of the same name are two entries.

  padCachePut       ( 'fw-files-both', 'cached' );
  padRateLimitClear ( 'fw-files-both' );
  padRateLimit      ( 'fw-files-both', 5 );

  $both = json_encode ( [ padCacheGet ( 'fw-files-both' ), padRateLimitRemaining ( 'fw-files-both', 5 ) ] );

?>
