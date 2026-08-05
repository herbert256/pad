<?php

  // Build strategy 'loop' for the cullen sequence: each term is the Cullen number
  // n * 2^n + 1, the power of two taken as a left shift - 3, 9, 25, 65, 161, 385, 897, ...
  //
  // The shift is machine-word bound, so exact terms run out at n = 57, which is where
  // generated.php stops; past that the value silently leaves the integer range.

  return (1 << $pqLoop) * $pqLoop + 1;

?>