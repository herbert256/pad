<?php

  // A ttl of 0 or less keeps nothing and runs the callback every time - also when the key
  // holds a value kept by an earlier call with a ttl of its own: padRemember read the key
  // first and answered the kept value, so a page asking for a fresh value - a ttl of 0 while
  // debugging - got the stale one until it expired.

  padCacheForget ( 'fw-remember-zero-kept' );

  $kept   = padRemember ( 'fw-remember-zero-kept', 600, fn () => 'kept' );
  $zero   = padRemember ( 'fw-remember-zero-kept', 0,   fn () => 'fresh' );
  $minus  = padRemember ( 'fw-remember-zero-kept', -5,  fn () => 'fresh again' );
  $still  = padCacheGet ( 'fw-remember-zero-kept' );

  padCacheForget ( 'fw-remember-zero-kept' );

?>
