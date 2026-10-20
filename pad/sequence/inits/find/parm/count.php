<?php

  // The first parameter of a type that takes none - no flags/parm - read as {sequence} reads
  // a first parameter when it names no type, parm/quick.php: a number is how many rows, and a
  // range or a list is the values the type plays over, in the mode the tag gives - {prime 5}
  // is five primes, {keep:prime '1..10'} the primes of 1 to 10, as {keep '1..10', prime} is.
  // It was the type's parameter, which the type does not read: {prime 5} and {prime '1..20'}
  // gave ten primes and {keep:prime '1..10'} the first ten primes, in silence. Anything else
  // is left for parm/name.php and parm/parm.php.

  if ( is_numeric ( $pqFindParm ) and $pqFindPositional ) {

    $pqRows     = $pqFindParm;
    $pqFindParm = '';

  } elseif ( is_string ( $pqFindParm ) and ( str_contains ( $pqFindParm, '..' ) or str_contains ( $pqFindParm, ';' ) ) ) {

    $padPrmName  = $pqSeq;
    $padPrmValue = TRUE;

    include PQ . 'plays/add.php';

    $pqSeq      = str_contains ( $pqFindParm, '..' ) ? 'range' : 'list';
    $pqParm     = $pqFindParm;
    $pqFindParm = '';

  }

?>
