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

  // The + 0 turns the float -0 that rounds a small negative value, and that fmod gives for a
  // negative multiple, into 0: printed, it read -0.

  return round ( $pqLoop / $pqParm ) * $pqParm + 0;

?>
