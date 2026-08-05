<?php

  // Build strategy 'loop' for the ceil sequence: each term is the loop value rounded up to
  // the next multiple of the parameter, which defaults to 1. {ceil 5} over 1..10 gives
  // 5, 5, 5, 5, 5, 10, 10, 10, 10, 10. Mostly used as a play, where {make ceil=5} rounds
  // another sequence's terms up to a multiple of five.

  if ( ! $pqParm )
    $pqParm = 1;

  return ceil ( $pqLoop / (int) $pqParm ) * $pqParm;

?>