<?php

  // Build strategy 'loop' for the icosihenagonal sequence: each term is the nth twenty-one
  // sided figurate number, n(19n - 17)/2 - 1, 21, 60, 118, 195, 291, ... The other polygonal
  // types define a pqXxx() function; this one computes the term inline from $pqLoop.

  return (19 * $pqLoop * $pqLoop - 17 * $pqLoop) / 2;

?>