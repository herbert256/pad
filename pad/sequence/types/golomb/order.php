<?php

  // Build strategy 'order' for the golomb sequence, the self-describing one where term n
  // says how often the value n occurs in it: 1, 2, 2, 3, 3, 4, 4, 4, 5, 5, 5, 6, 6, ...
  //
  // Implements a(1) = 1, a(n) = 1 + a(n - a(a(n-1))) by reading earlier terms back out of
  // $pqOrder, which build/one.php fills as the build runs. Every index is one lower than
  // the mathematics suggests because $pqOrder is 0-based while $pqLoop counts terms from 1.

  if ( $pqLoop == 1 ) return 1;

  return 1 + $pqOrder [ $pqLoop - ($pqOrder [ $pqOrder [ $pqLoop - 2 ] - 1 ] + 1) ];

?>