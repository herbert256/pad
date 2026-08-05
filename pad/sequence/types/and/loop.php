<?php

  // Build strategy 'loop' for the and sequence: each term is the bitwise AND of the loop
  // value and the parameter, so {and 12} over 1, 2, 3, ... gives 0, 0, 0, 4, 4, 4, 4, 8.
  // Despite the name in the documentation the operator is bitwise, not logical; the
  // parameter is cast to int so a string parm still masks sensibly.

  return $pqLoop & (int) $pqParm;

?>