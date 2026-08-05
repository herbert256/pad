<?php

  // Build strategy 'order': each term is computed from the terms before it (fibonacci and
  // the other recurrences), so the sequence can only be generated from term 1 upwards in
  // steps of 1, whatever from= and increment= asked for.
  //
  // Starts the $pqOrder history the type's order.php reads back, keeps the requested start
  // in $pqOrderFrom so build/one.php can compute the early terms without emitting them,
  // then forces from/increment and runs the loop iterator.

  $pqOrder     = [];
  $pqOrderFrom = $pqFrom;

  $pqFrom = 1;
  $pqInc  = 1;

  include PQ . 'build/types/type/loop.php';

?>