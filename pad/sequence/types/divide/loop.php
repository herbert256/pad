<?php

  // Build strategy 'loop' for the divide sequence: each term is the loop value divided by
  // the parameter, so {divide 2, from=1} gives 0.5, 1, 1.5, 2, ... Terms are floats
  // whenever the division is not exact, and a parameter of zero is not caught here.

  return $pqLoop / $pqParm;

?>