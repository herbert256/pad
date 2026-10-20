<?php

  // Membership test for partition, 1, 1, 2, 3, 5, 7, 11, ...: the terms are walked until one
  // reaches x - some 400 of them at the most before the integer range ends. build/check.php
  // asks it for {keep partition}, {remove partition} and {flag partition}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  include_once PT . 'partition/function.php';

  function pqBoolPartition ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

    for ( $n = 0; is_int ( $term = pqPartition ( $n ) ) and $term < $x; $n++ ) ;

    return $term == $x;

  }

?>
