<?php

  // Build strategy 'loop' for the exponentiation sequence: each term is the loop value
  // raised to the parameter, so {exponentiation 3} gives 1, 8, 27, 64, ... - a general
  // version of square, cubic and biquadratic. The power type is the mirror image of this
  // one: it raises the parameter to the loop value.

  // 0 raised to a negative power is a division by zero and has no term: PHP deprecates the
  // ** that would make INF of it, and exponentiation=-1 from 0 ended the request on that.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  if ( $pqLoop == 0 and $pqParm < 0 )
    return FALSE;

  return $pqLoop ** $pqParm;

?>
