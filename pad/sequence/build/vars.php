<?php

  // Prepares the per-build globals, just before the sequence is generated.
  //
  // Second step of build/build.php. Records sequence and strategy in $pqInfo for the debug
  // output, detects a random 'from..to' parameter and keeps the range text in
  // $pqRandomParm so build/parm.php can re-roll it per term, saves the sequence name in
  // $pqOrgName for the extra data field, and sets $pqParmStore when the parameter names a
  // stored sequence instead of a value.

  if ( $padInfo ) {
    $pqInfo ['sequences'] [] = $pqSeq;
    $pqInfo ['builds']    [] = $pqBuild;
  }

  if ( str_contains ( $pqParm, '..' ) and $pqSeq != 'range' ) {
    $pqRandomParm = $pqParm;
    pqRandomParm ( $pqParm );
  } else
    $pqRandomParm = FALSE;

  $pqOrgName = $pqSeq;

  // A store is named by a name: a parameter that is a number - a stored 1e20 - is no key to
  // look up, and as one ended the request on "The float 1.0E+20 is not representable as an int".

  $pqParmStore = ( is_string ( $pqParm ) and $pqParm and isset ( $pqStore [$pqParm] ) ) ? $pqParm : '';

?>
