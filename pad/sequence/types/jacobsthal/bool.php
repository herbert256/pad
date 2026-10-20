<?php

  // Membership test for jacobsthal, 0, 1, 1, 3, 5, 11, ...: the recurrence is walked until a
  // term reaches x - 64 steps at the most before the integer range ends. build/check.php asks
  // it for {keep jacobsthal}, {remove jacobsthal} and {flag jacobsthal}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolJacobsthal ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return FALSE;

    $x = (int) $x;

    for ( $term = 0, $i = 1; $term < $x and is_int ( $term ); $i++ )
      $term = 2 * $term + ( $i % 2 ? 1 : -1 );

    return $term === $x;

  }

?>
