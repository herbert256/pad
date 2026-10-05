<?php

  // A function the page calls for every row reads its settings once: padOnce answers the
  // first result each later time its line is reached.

  $reads = 0;

  $currency = function () use ( &$reads ) {
    return padOnce ( function () use ( &$reads ) { $reads++; return 'EUR'; } );
  };

  $orders = [];

  foreach ( [ 120, 75, 310 ] as $amount )
    $orders [] = [ 'amount' => $amount, 'currency' => $currency () ];

?>
