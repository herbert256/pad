<?php

  // Membership test for derangement, 1, 0, 1, 2, 9, 44, ...: the recurrence is walked until a
  // term passes x - from !2 on the terms climb, so 0 and 1 are let through the first steps.
  // build/check.php asks it for {keep derangement}, {remove derangement} and {flag
  // derangement}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolDerangement ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return FALSE;

    $x = (int) $x;

    for ( $term = 1, $i = 1; is_int ( $term ) and ( $term <= $x or $i <= 2 ); $i++ ) {

      if ( $term == $x )
        return TRUE;

      $term = $i * $term + ( $i % 2 ? -1 : 1 );

    }

    return FALSE;

  }

?>
