<?php

  // Build strategy 'make' for the multiple sequence: the same rounding up to the next
  // multiple of the parameter as loop.php, kept in its own file so pqBuild() can select it
  // for a play. {make multiple=5} therefore lifts every term of another sequence to the
  // next multiple of five, while a plain {multiple 5} build goes through loop.php.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  // A step of 0 - a store's term handed over as the parameter - makes no term, as in loop.php:
  // rounded up to a multiple of nothing, the request ended on "Division by zero".

  // A parameter that is 0 - spelled 0, '0.0', '-0' or '00' - makes no term. ! $pqParm let the
  // strings '0.0', '-0' and '00' through, which are true, and {sequence '1..3', make, multiple='0.0'}
  // ended the request on "Division by zero".

  if ( is_numeric ( $pqParm ) ? $pqParm == 0 : ! $pqParm )
    return FALSE;

  return pqCeilMultiple ( $pqLoop, $pqParm );

?>
