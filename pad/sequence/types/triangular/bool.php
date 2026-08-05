<?php

  // Membership predicate and generation path for triangular: pqBoolTriangular($n) is TRUE
  // when n = k(k+1)/2 for a whole k - 1, 3, 6, 10, 15, 21, 28, ...
  //
  // Solves that as a quadratic rather than searching: k^2 + k - 2n = 0 has a whole positive
  // root exactly when n is triangular. The only file in the type, so pqBuild() runs the
  // whole range through it.

function pqBoolTriangular ($num) {

    if ($num < 0)
        return false;

    $c = (-2 * $num);
    $b = 1; $a = 1;
    $d = ($b * $b) - (4 * $a * $c);

    if ($d < 0)
        return false;

    $root1 = (-$b + (float)sqrt($d)) / (2 * $a);

    $root2 = (-$b - (float)sqrt($d)) / (2 * $a);

    if ($root1 > 0 && floor($root1) == $root1)
        return true;

    if ($root2 > 0 && floor($root2) == $root2)
        return true;

    return false;
}

?>