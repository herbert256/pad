<?php

  // Marker file, never included: its existence is what makes 'rows' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // rows=N caps how many terms the run returns: build/one.php ends the build as soon as
  // $pqResult holds that many. inits/parms.php reads it into $pqRows and inits/limits.php
  // fills in what was left open - the configured $padSeqDefaultRows normally, PHP_INT_MAX
  // when pull=, stop=, to= or a 'build' strategy already decides where the terms end.

  return TRUE;

?>