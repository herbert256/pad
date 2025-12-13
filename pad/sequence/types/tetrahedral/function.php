<?php


/**
 * Calculates the nth tetrahedral number.
 *
 * Tetrahedral numbers are figurate numbers representing
 * a tetrahedron. Formula: n*(n+1)*(n+2)/6.
 *
 * @param int $n The index in the sequence.
 *
 * @return int The nth tetrahedral number.
 */
function pqTetrahedral ($n) {

  return ($n*($n+1)*($n+2))/6;

}

?>