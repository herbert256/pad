<?php

  // Build strategy 'loop' for the divide sequence: each term is the loop value divided by
  // the parameter, so {divide 2, from=1} gives 0.5, 1, 1.5, 2, ... Terms are floats
  // whenever the division is not exact.
  //
  // init.php refuses a parameter of zero, but one written as a range - divide='0..3' - is
  // drawn afresh for every candidate and can come up zero here, where the answer is that
  // this candidate has no term rather than that the run is wrong.

  if ( ! $pqParm )
    return FALSE;

  return $pqLoop / $pqParm;

?>