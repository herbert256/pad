<?php

  // Loop build for or: the bitwise OR of the loop value and the parameter, n | $pqParm.
  // nor/loop.php includes this file and complements what it returns.

  return $pqLoop | (int) $pqParm;

?>