<?php

  // Loop build for round: rounds each loop value to the nearest multiple of the parameter,
  // which defaults to 1 and then leaves the value alone. So round=10 turns 1, 2, 3, ...
  // into runs of 0, 10, 20, ...; add unique to get the multiples themselves.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  if ( ! $pqParm )
    $pqParm = 1;

  // pqZero turns the float -0 that rounds a small negative value, and that fmod gives for a
  // negative multiple, into 0: printed, it read -0.

  // A multiple of -10 is one of 10, the nearest either way. In whole numbers when both are
  // whole, pqRoundMultiple.
  return pqZero ( pqRoundMultiple ( $pqLoop, abs ( $pqParm === TRUE ? 1 : $pqParm ) ) );

?>
