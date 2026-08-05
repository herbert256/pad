<?php

  // Range setup for the multiple sequence, run before generation by sequence/inits/init.php:
  // the step becomes the parameter and the start is lifted to the first multiple of it at or
  // above from=, so {multiple 3, from=4} runs 6, 9, 12, ... The loop then lands on multiples
  // by construction and loop.php has nothing left to round.

  $pqInc   = $pqParm;
  $pqFrom = ceil ( $pqFrom / $pqParm) * $pqParm;

?>