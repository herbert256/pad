<?php

  // Loop build for xor: the bitwise XOR of the loop value and the parameter, n ^ $pqParm.
  // xnor/loop.php includes this file and complements what it returns.

  return $pqLoop ^ (int) $pqParm;

?>