<?php

  // Loop build for nor: the bitwise NOR of the loop value and the parameter,
  // ~($pqLoop | $pqParm) - or/loop.php does the OR and this complements its result.

  return ~ include PT . 'or/loop.php';

?>