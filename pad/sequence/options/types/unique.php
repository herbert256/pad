<?php

  // Marker file, never included: its existence is what makes 'unique' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // unique drops a candidate whose value is already among the terms collected so far, so
  // the run yields distinct values. inits/parms.php reads it into $pqUnique and
  // build/one.php tests it while building - unlike the dedup action, which cleans up the
  // finished list afterwards.

  return TRUE;

?>