<?php

  padCachePut ( 'fw-ttl-zero', 'kept' );

  $zero = json_encode ( [ padCachePut ( 'fw-ttl-zero', 'not kept', 0 ), padCacheHas ( 'fw-ttl-zero' ) ] );

  $negative = json_encode ( [ padCachePut ( 'fw-ttl-negative', 'not kept', -5 ),
                              padCacheHas ( 'fw-ttl-negative' ) ] );

  $past = json_encode ( [ padCachePut ( 'fw-ttl-past', 'not kept', new DateTimeImmutable ( '-1 minute' ) ),
                          padCacheHas ( 'fw-ttl-past' ) ] );

  $future = json_encode ( [ padCachePut ( 'fw-ttl-future', 'kept', new DateTimeImmutable ( '+1 hour' ) ),
                            padCacheGet ( 'fw-ttl-future' ) ] );

  $interval = json_encode ( [ padCachePut ( 'fw-ttl-interval', 'kept', new DateInterval ( 'PT1H' ) ),
                              padCacheGet ( 'fw-ttl-interval' ) ] );

  $forever = json_encode ( [ padCachePut ( 'fw-ttl-forever', 'kept', NULL ), padCacheGet ( 'fw-ttl-forever' ),
                             padCachePut ( 'fw-ttl-infinite', 'kept', INF ), padCacheGet ( 'fw-ttl-infinite' ) ] );

  $text = json_encode ( [ padCachePut ( 'fw-ttl-text', 'kept', '60' ), padCacheGet ( 'fw-ttl-text' ) ] );

  // One second, then a little more than a second later the entry is gone.

  padCachePut ( 'fw-ttl-short', 'kept', 1 );

  $before = padCacheGet ( 'fw-ttl-short', 'gone' );

  usleep ( 1100000 );

  $after = json_encode ( [ padCacheGet ( 'fw-ttl-short', 'gone' ), padCacheHas ( 'fw-ttl-short' ) ] );

?>
