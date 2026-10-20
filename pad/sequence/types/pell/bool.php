<?php

  // Membership test for the pell sequence, 0, 1, 2, 5, 12, 29, ...: the recurrence P(n) =
  // 2P(n-1) + P(n-2) from P(0) = 0 and P(1) = 1, walked until a term reaches x - some fifty
  // steps at the most before the integer range ends. build/check.php asks it for {keep pell},
  // {remove pell} and {flag pell}; without it the check read the PADpell table, which starts
  // at P(1) and turned down the 0 the type makes at position 0.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolPell ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return FALSE;

    $x = (int) $x;

    for ( $a = 0, $b = 1; $a < $x and is_int ( $b ); [ $a, $b ] = [ $b, 2 * $b + $a ] ) ;

    return $a === $x;

  }

?>
