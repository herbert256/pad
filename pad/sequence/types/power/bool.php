<?php

  // Membership predicate for power: pqBoolPower($x, $y) is TRUE when x is a whole power of
  // y, decided by dividing y out of x and asking whether 1 is left.
  //
  // $y is the sequence parameter, so {keep power=2} keeps 2, 4, 8, 16, ... - x = 1 is left
  // out to match loop.php, which starts at the first power rather than at y^0. Membership
  // only: generation goes through loop.php, which pqBuild() prefers.
  //
  // A base of 0, 1 or -1 has no repeated division to do and would spin forever or divide by
  // zero, so those are answered directly, as is a missing or non-numeric parameter. An x of
  // 0 is a fixed point of the division for the same reason and no power is ever 0, so it and
  // anything below it are answered too.

  function pqBoolPower ( $x, $y ) {

    if ( ! is_numeric ( $y ) or abs ( $y ) < 2 )
      return ( $x == $y );

    if ( $x == 1 and $y != 1 )
      return FALSE;

    if ( $x < 1 )
      return FALSE;

    while ($x % $y == 0)
        $x = $x / $y;

    if ($x == 1)
      return TRUE;
    else
      return FALSE;

  }

?>