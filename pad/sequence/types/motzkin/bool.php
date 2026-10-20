<?php

  // Membership test for motzkin, 1, 1, 2, 4, 9, 21, ...: the terms are walked until one
  // reaches x - 45 of them at the most before the integer range ends. build/check.php asks it
  // for {keep motzkin}, {remove motzkin} and {flag motzkin}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  include_once PT . 'motzkin/function.php';

  function pqBoolMotzkin ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

    for ( $n = 0; is_int ( $term = pqMotzkin ( $n ) ) and $term < $x; $n++ ) ;

    return $term == $x;

  }

?>
