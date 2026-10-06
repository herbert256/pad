<?php

  // Build strategy 'loop' for the multiply sequence: each term is the loop value times the
  // parameter, so {multiply 3} gives 3, 6, 9, 12, ... The type declares flags/parm, so the
  // tag's first parameter is the multiplier rather than a row count; as a play,
  // {make multiply=3} scales another sequence's terms.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  return $pqLoop * $pqParm;

?>
