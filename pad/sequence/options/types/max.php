<?php

  // Marker file, never included: its existence is what makes 'max' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // The build's upper bound is in fact written maximal=: inits/parms.php reads
  // $pqParms['maximal'] into $pqMax and build/one.php drops any candidate above it. No
  // reader looks for a 'max' parameter, so this entry only reserves the short name. Same
  // for min.php.

  return TRUE;

?>