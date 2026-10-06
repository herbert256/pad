<?php

  // Loop build for xor: the bitwise XOR of the loop value and the parameter, n ^ $pqParm.
  // xnor/loop.php includes this file and complements what it returns.

  // A value that is no whole number has no term here - a word in the list a make play is
  // handed ended the request on the arithmetic, and a fraction on PHP's deprecation of it
  // as an integer.

  if ( ! pqBoolWhole ( $pqLoop ) )
    return FALSE;

  return $pqLoop ^ (int) $pqParm;

?>
