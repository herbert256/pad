<?php

  // Build strategy 'order' for the fibonacci sequence: terms 1 and 2 are the seeds 0 and 1,
  // every later term is the sum of the two before it, delegated to fibonacci/go.php -
  // 0, 1, 1, 2, 3, 5, 8, 13, 21, ...
  //
  // Because each term needs the terms before it, build/types/order.php forces generation
  // from term 1 in steps of 1 whatever from= and increment= asked for, and suppresses the
  // terms below from= rather than skipping their computation. generated.php caches the 93
  // terms that still fit in a PHP integer.

  if ( $pqLoop == 1 ) return 0;
  if ( $pqLoop == 2 ) return 1;

  return include PT . 'fibonacci/go.php';

?>