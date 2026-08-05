<?php

  // Build strategy 'fixed' for the mersenne sequence: a literal list of the first twelve
  // Mersenne primes, 2^p - 1 for p = 2, 3, 5, 7, 13, 17, 19, 31, 61, 89, 107 and 127 -
  // so 3, 7, 31, 127, 8191, 131071, 524287, ...
  //
  // Only the first nine fit in a PHP integer; the last three are parsed as floats, and
  // build/one.php stops the build when it meets a float beyond PHP_INT_MAX, so in practice
  // the sequence ends at 2^61 - 1.

  return [ 3, 7, 31, 127, 8191, 131071, 524287, 2147483647, 2305843009213693951, 618970019642690137449562111, 162259276829213363391578010288127, 170141183460469231731687303715884105727];

?>