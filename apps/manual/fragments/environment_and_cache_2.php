<?php

  // The first request of the next ten minutes runs the callback; the others get what it
  // left in the cache, without asking the source again.

  $rates = padRemember ( 'manual-rates', 600, function () {
    return [ [ 'code' => 'EUR', 'rate' => '1.00' ],
             [ 'code' => 'USD', 'rate' => '1.08' ],
             [ 'code' => 'GBP', 'rate' => '0.86' ] ];
  } );

  padCachePut ( 'manual-notice', 'Maintenance on Sunday night', 3600 );

  $notice  = padCacheGet ( 'manual-notice', 'No notice' );
  $nothing = padCacheGet ( 'manual-nothing-kept', 'Nothing kept under this key' );

?>
