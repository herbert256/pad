<?php

  $values = [ 'string' => 'héllo wörld', 'zero' => 0, 'zeroText' => '0', 'empty' => '',
              'false' => FALSE, 'null' => NULL, 'float' => 2.5,
              'nested' => [ 'a' => [ 'b' => [ 1, 2 ] ], 'c' => NULL ], 'emptyList' => [] ];

  $back = [];

  foreach ( $values as $name => $value ) {
    padCachePut ( "fw-put-$name", $value );
    $back [$name] = padCacheGet ( "fw-put-$name", 'the default' );
  }

  $stored = json_encode ( $back, JSON_UNESCAPED_UNICODE );
  $same   = json_encode ( $back === $values );

  padCacheForget ( 'fw-put-missing' );

  $missing = padCacheGet ( 'fw-put-missing', 'the default' );
  $closure = padCacheGet ( 'fw-put-missing', function () { return 'from a closure'; } );
  $nothing = json_encode ( padCacheGet ( 'fw-put-missing' ) );

  $has = json_encode ( [ padCacheHas ( 'fw-put-null' ), padCacheHas ( 'fw-put-false' ),
                         padCacheHas ( 'fw-put-missing' ) ] );

  padCachePut ( 42, 'an integer key' );

  $intKey = padCacheGet ( '42' );

  $forget = json_encode ( [ padCacheForget ( 'fw-put-string' ), padCacheForget ( 'fw-put-string' ),
                            padCacheHas ( 'fw-put-string' ), padCacheGet ( 'fw-put-string', 'gone' ) ] );

?>
