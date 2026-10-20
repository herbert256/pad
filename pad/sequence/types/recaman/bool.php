<?php

  // Membership test for the recaman sequence: whether x is among its terms as far as a run
  // reaches, $padSeqMaxTries positions. The terms are made by pqRecamanMade () in function.php,
  // as far as the test needs them - doubled while x has not turned up - and kept for the
  // request with the values they took as keys, so the question is an isset. build/check.php
  // asks it for {keep recaman}, {remove recaman} and {flag recaman}.
  //
  // Without it the check read the PADrecaman table, the first 10,000 terms: the sequence comes
  // back below values it passed - 19 first turns up at position 99735 - so a miss below the
  // table's largest value was no answer, and the table not ending on its largest value was
  // taken as the whole sequence: 30,009 values below 37,947, and every term past the table,
  // were answered no member.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  include_once PT . 'recaman/function.php';

  function pqBoolRecaman ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return FALSE;

    $x    = (int) $x;
    $most = $GLOBALS ['padSeqMaxTries'] ?? 1000000;
    $made = &pqRecamanMade ();

    while ( ! isset ( $made ['seen'] [$x] ) and count ( $made ['terms'] ) < $most )
      pqRecamanUpTo ( min ( $most, max ( 1024, 2 * count ( $made ['terms'] ) ) ) );

    return isset ( $made ['seen'] [$x] );

  }

?>
