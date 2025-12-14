<?php


/**
 * Checks if a number is triangular.
 *
 * A triangular number satisfies n*(n+1)/2 = num for some n.
 * Uses quadratic formula to find if valid integer n exists.
 *
 * @param int $num The number to check.
 *
 * @return bool TRUE if num is a triangular number.
 */
function pqBoolTriangular ($num) {

    if ($num < 0)
        return false;

    // Considering the equation
    // n*(n+1)/2 = num
    // The equation is :
    // a(n^2) + bn + c = 0";
    $c = (-2 * $num);
    $b = 1; $a = 1;
    $d = ($b * $b) - (4 * $a * $c);

    if ($d < 0)
        return false;

    // Find roots of equation
    $root1 = (-$b + (float)sqrt($d)) / (2 * $a);

    $root2 = (-$b - (float)sqrt($d)) / (2 * $a);

    // checking if root1 is natural
    if ($root1 > 0 && floor($root1) == $root1)
        return true;

    // checking if root2 is natural
    if ($root2 > 0 && floor($root2) == $root2)
        return true;

    return false;
}

?>