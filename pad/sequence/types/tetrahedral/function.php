<?php

  // Function build for tetrahedral: pqTetrahedral($n) = n(n+1)(n+2)/6, the running total of
  // the triangular numbers and so the number of spheres in a triangular pyramid of n
  // layers - 1, 4, 10, 20, 35, 56, 84, ...

function pqTetrahedral ($n) {

  return ($n*($n+1)*($n+2))/6;

}

?>