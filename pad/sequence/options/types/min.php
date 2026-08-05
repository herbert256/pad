<?php

  // Marker file, never included: its existence is what makes 'min' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // The build's lower bound is in fact written minimal=: inits/parms.php reads
  // $pqParms['minimal'] into $pqMin and build/one.php drops any candidate below it. No
  // reader looks for a 'min' parameter, so this entry only reserves the short name. Same
  // for max.php.

  return TRUE;

?>