<?php

  // padNumberAbbreviate: K M B T Q, the unit chosen on the number as it will be written
  // (999999 is 1M), at most precision decimals with the trailing zeros dropped, below 1000
  // no letter, the sign kept, Q growing past it. padNumberForHumans: the same in words.

  $short = json_encode ( array_map ( fn ( $args ) => padNumberAbbreviate ( ...$args ), [
    [ 0 ], [ 999 ], [ 1000 ], [ 1500 ], [ 1500, 1 ], [ 1000, 1 ], [ 999999 ], [ 999.4 ], [ 999.6 ],
    [ 1234567, 2 ], [ 2.5e9, 1 ], [ 1e12 ], [ 3e15 ], [ 5e18 ], [ -1500 ], [ -1500, 1 ], [ '12.5' ],
    [ '0' ], [ 999950, 1 ], [ -0.4 ], [ NULL ], [ '' ]
  ] ) );

  $words = json_encode ( array_map ( fn ( $args ) => padNumberForHumans ( ...$args ), [
    [ 1000 ], [ 1500000, 1 ], [ 2e9 ], [ 999 ], [ -1500 ], [ 1e15 ], [ 1234, 2 ], [ 7e12 ], [ NULL ]
  ] ) );

?>
