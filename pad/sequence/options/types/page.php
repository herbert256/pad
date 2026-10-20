<?php

  // Marker file, never included: its existence is what makes 'page' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // page=N pages through the terms as it pages through the rows of a data tag, rows= being
  // the page's size: inits/parms.php reads it into $pqPage, inits/limits.php widens the run
  // for it and exits/page.php cuts the page out. Without this marker 'page' was no option
  // name to the subsystem, so a store could be named page - push='page', or a loop pair named
  // page - and every later page= pulled that store: {sequence prime, page=2, rows=3} answered
  // the store's terms made into primes.

  return TRUE;

?>
