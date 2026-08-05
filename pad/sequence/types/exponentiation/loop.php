<?php

  // Build strategy 'loop' for the exponentiation sequence: each term is the loop value
  // raised to the parameter, so {exponentiation 3} gives 1, 8, 27, 64, ... - a general
  // version of square, cubic and biquadratic. The power type is the mirror image of this
  // one: it raises the parameter to the loop value.

  return $pqLoop ** $pqParm;

?>