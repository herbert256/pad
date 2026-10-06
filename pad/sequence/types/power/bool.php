<?php

  // Membership predicate for power: pqBoolPower($x, $y) is TRUE when x is y raised to a whole
  // k of 1 or more - the terms loop.php makes, $y ** $k. Membership only: generation goes
  // through loop.php, which pqBuild() prefers.
  //
  // $y is the sequence parameter, so {keep power=2} keeps 2, 4, 8, 16, ... - x = 1 is left
  // out to match loop.php, which starts at the first power rather than at y^0. The k that
  // could give x is the logarithm of x to the base y, so the whole numbers next to it are
  // tried with the same ** loop.php uses. Dividing y out of x until 1 was left knew only
  // whole positive bases: a base of -2 makes -2, 4, -8, 16, and -2 and -8 were answered no,
  // and a base of 2.5 ended the request on PHP's deprecation of the fraction inside %.
  //
  // A base of 1, -1 or 0 makes the same one or two values over and over, and a parameter
  // that is no number - a bare power is the TRUE read as 1 - is compared as it stands.

  function pqBoolPower ( $x, $y ) {

    if ( $y === TRUE )
      $y = 1;

    if ( ! is_numeric ( $y ) )
      return ( $x == $y );

    if ( ! is_numeric ( $x ) )
      return FALSE;

    if ( $y == 0 or $y == 1 )
      return ( $x == $y );

    if ( $y == -1 )
      return ( $x == 1 or $x == -1 );

    if ( $x == 0 )
      return FALSE;

    $k = (int) round ( log ( abs ( $x ) ) / log ( abs ( $y ) ) );

    for ( $i = max ( 1, $k - 1 ); $i <= $k + 1; $i++ )
      if ( $y ** $i == $x )
        return TRUE;

    return FALSE;

  }

?>
