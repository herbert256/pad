<?php

  // Marker file, never included: its existence is what makes 'try' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // try=N caps how many candidates the build may weigh before giving up, which is what
  // stops a heavily filtered sequence from searching forever: build/one.php ends the build
  // once $pqTries passes it. inits/parms.php reads it into $pqTry and inits/limits.php fills
  // in the configured $padSeqDefaultTries, or PHP_INT_MAX when pull=, stop=, to= or a
  // 'fixed' strategy already bounds the run.

  return TRUE;

?>