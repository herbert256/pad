<?php

  // Loop build for negation: every loop value with its sign flipped, -1, -2, -3, ...

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  // The + 0 turns the float -0 into 0: printed, it read -0. It comes of a float 0 - a listed
  // 0.0, or a value that rounds to 0 - and a negative sign: - 0.0.

  return - $pqLoop + 0;

?>
