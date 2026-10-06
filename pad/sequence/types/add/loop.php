<?php

  // Build strategy 'loop' for the add sequence: each term is the loop value plus the
  // parameter, so {add 5, from=1} gives 6, 7, 8, ... The type declares flags/parm, so the
  // tag's first parameter is the addend and not a row count. As a play, {make add=5} shifts
  // every term of another sequence by five.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  return $pqLoop + $pqParm;

?>
