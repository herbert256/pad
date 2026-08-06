<?php

  // Range setup for the multiple sequence, run before generation by sequence/inits/init.php:
  // the step becomes the parameter and the start is lifted to the first multiple of it at or
  // above from=, so {multiple 3, from=4} runs 6, 9, 12, ... The loop then lands on multiples
  // by construction and loop.php has nothing left to round.
  //
  // The parameter is the step, so it has to be a number and cannot be zero - stepping by
  // nothing arrives nowhere, and dividing from= by it ended the request before the loop was
  // even reached.

  include PQ . 'inits/number.php';

  if ( is_numeric ( $pqParm ) and ! $pqParm )
    return padError ( 'The multiple sequence needs a step of 1 or more' );

  // Only a parameter that is already a number can set the range up. One written as a range
  // or naming a store is not resolved until each candidate is built, so there is nothing to
  // divide by yet and the loop is left as it stands.

  if ( ! is_numeric ( $pqParm ) )
    return;

  $pqInc   = $pqParm;
  $pqFrom = ceil ( $pqFrom / $pqParm) * $pqParm;

?>