<?php

  // Build strategy 'loop' for the divide sequence: each term is the loop value divided by
  // the parameter, so {divide 2, from=1} gives 0.5, 1, 1.5, 2, ... Terms are floats
  // whenever the division is not exact.
  //
  // init.php refuses a parameter of zero, but one written as a range - divide='0..3' - is
  // drawn afresh for every candidate and can come up zero here, where the answer is that
  // this candidate has no term rather than that the run is wrong.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  if ( ! $pqParm )
    return FALSE;

  // The + 0 turns the float -0 into 0: printed, it read -0. It comes of a float 0 - a listed
  // 0.0, or a value that rounds to 0 - and a negative sign: 0.0 / -2.

  return $pqLoop / $pqParm + 0;

?>
