<?php

  // Loop build for xnor: the bitwise XNOR of the loop value and the parameter,
  // ~($pqLoop ^ $pqParm) - xor/loop.php does the XOR and this complements its result.

  return ~ include PT . 'xor/loop.php';

?>