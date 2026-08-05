<?php

  // Marker file, never included: its existence is what makes 'skip' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // skip=N discards the opening candidates. build/one.php tests it against the try counter
  // rather than against the terms collected, so it drops the first N values offered, not
  // the first N kept. inits/parms.php reads it into $pqSkip and resolves an 'a..b' form.

  return TRUE;

?>