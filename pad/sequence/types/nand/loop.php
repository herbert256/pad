<?php

  // Loop build for nand: the bitwise NAND of the loop value and the parameter,
  // ~($pqLoop & $pqParm) - and/loop.php does the AND and this complements its result.

  return ~ include PT . 'and/loop.php';

?>