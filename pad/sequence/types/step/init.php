<?php

  // Range setup for step, run before generation: makes the parameter the increment, so
  // {sequence step=5} counts 1, 6, 11, 16, ... The type's loop.php then only has to pass
  // each value through.
  //
  // The parameter becomes the increment, so it has to be a number; a step under 1 is left
  // to build/types/type/loop.php, which ends a walk that would never advance.

  include PQ . 'inits/number.php';

  $pqInc = $pqParm;

  // The step is set once, before the first candidate, so a parameter written as a range is
  // drawn here and one naming a store gives its first term - put in place as they came, the
  // text '1..3' or the store's name was added to the candidate and the request ended on "A
  // non-numeric value encountered". A bare step is the 1 the other types read TRUE as.

  pqRandomParm ( $pqInc );

  if ( ! is_numeric ( $pqInc ) and is_scalar ( $pqInc ) and isset ( $pqStore [$pqInc] ) )
    $pqInc = reset ( $pqStore [$pqInc] );

  if ( ! is_numeric ( $pqInc ) )
    $pqInc = 1;

?>
