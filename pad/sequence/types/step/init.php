<?php

  // Range setup for step, run before generation: makes the parameter the increment, so
  // {sequence step=5} counts 1, 6, 11, 16, ... The type's loop.php then only has to pass
  // each value through.
  //
  // The parameter becomes the increment, so it has to be a number; a step under 1 is left
  // to build/types/type/loop.php, which ends a walk that would never advance.

  include PQ . 'inits/number.php';

  $pqInc = $pqParm;

?>