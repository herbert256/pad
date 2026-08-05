<?php

  // Fixed build for perfect: the first ten perfect numbers - equal to the sum of their
  // proper divisors - listed literally, since searching for them past 8128 is hopeless.
  //
  // Only the first eight are usable. From 2658455991569831744654692615953842176 on the
  // literals exceed PHP_INT_MAX and become floats, and build/one.php ends the build at the
  // first float out of integer range.

  return [ 6, 28, 496, 8128, 33550336, 8589869056, 137438691328, 2305843008139952128, 2658455991569831744654692615953842176, 191561942608236107294793378084303638130997321548169216];

?>