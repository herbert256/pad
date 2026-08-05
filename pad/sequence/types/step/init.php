<?php

  // Range setup for step, run before generation: makes the parameter the increment, so
  // {sequence step=5} counts 1, 6, 11, 16, ... The type's loop.php then only has to pass
  // each value through.

  $pqInc = $pqParm;

?>