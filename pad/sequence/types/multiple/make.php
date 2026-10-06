<?php

  // Build strategy 'make' for the multiple sequence: the same rounding up to the next
  // multiple of the parameter as loop.php, kept in its own file so pqBuild() can select it
  // for a play. {make multiple=5} therefore lifts every term of another sequence to the
  // next multiple of five, while a plain {multiple 5} build goes through loop.php.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  return pqCeilMultiple ( $pqLoop, $pqParm );

?>
