<?php

  // Membership test for the vanEck sequence: whether x is among its terms as far as a run
  // reaches, $padSeqMaxTries positions. The terms are made by pqVanEckUpTo () in function.php,
  // as far as the test needs them - doubled while x has not turned up - and kept for the
  // request with the position each value was last seen at, so the question is an isset.
  // build/check.php asks it for {keep vanEck}, {remove vanEck} and {flag vanEck}, as
  // recaman/bool.php does for recaman.
  //
  // Without it the check read the PADvanEck table, the first 10,000 terms: a value first turns
  // up long after smaller and larger ones - 251 at position 13191 - and the table does not end
  // on its largest value, so it was taken as the whole sequence: keep='vanEck' over 245..255
  // dropped 251, 280 and 295 were no member, and so was every value past 9344, the table's
  // largest - flag='vanEck' over 20000;50000 answered 0 0, made at positions 770341 and 242161.
  //
  // A term is a distance back to a position of 1 or more, so the term at position n is at most
  // n - 2: a value that would stand past the reach is answered at once, without the walk.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  include_once PT . 'vanEck/function.php';

  function pqBoolVanEck ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return FALSE;

    $x    = (int) $x;
    $most = $GLOBALS ['padSeqMaxTries'] ?? 1000000;

    if ( $x > $most - 2 )
      return FALSE;

    $made = &pqVanEckMade ();

    while ( ! isset ( $made ['last'] [$x] ) and $made ['terms'] [ count ( $made ['terms'] ) ] !== $x
            and count ( $made ['terms'] ) < $most )
      pqVanEckUpTo ( min ( $most, max ( 1024, 2 * count ( $made ['terms'] ) ) ) );

    return isset ( $made ['last'] [$x] ) or $made ['terms'] [ count ( $made ['terms'] ) ] === $x;

  }

?>
