<?php

  // Loop build for power, the strategy pqBuild() picks for this type: the parameter raised
  // to the loop value, so power=2 gives 2, 4, 8, 16, 32, ... Membership questions go to
  // bool.php instead.

  // 0 raised to a negative power is a division by zero and has no term: PHP deprecates the
  // ** that would make INF of it, and power=0 from -1 ended the request on that.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  if ( $pqParm == 0 and $pqLoop < 0 )
    return FALSE;

  // The + 0 turns the float -0 into 0: printed, it read -0. A negative base raised to a far
  // odd power - (-0.1) ** 401 - underflows to it.

  return $pqParm ** $pqLoop + 0;

?>
