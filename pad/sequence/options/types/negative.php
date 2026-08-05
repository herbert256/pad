<?php

  // Marker file, never included: its existence is what makes 'negative' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // negative inverts an action's selection: the result becomes everything the action did
  // NOT pick, so {pull:nums first=5, negative} yields all but the first five.
  // inits/parms.php reads it into $pqNegative, and actions/negative/negative.php rebuilds
  // $pqResult from the pre-action snapshot after each action has run.

  return TRUE;

?>