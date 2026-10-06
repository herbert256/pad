<?php

  // Build strategy 'loop' for the identity sequence: the term is the loop value itself, the
  // unary plus doing nothing but forcing it to a number. Gives the plain counter 1, 2, 3,
  // ... and serves as the neutral sequence to hang from=, to=, increment= and plays off.
  // The loop type is the same idea with an init.php that reads a row count from the parm.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  return + $pqLoop;

?>
