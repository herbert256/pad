<?php

  // Marker file, never included: its existence is what makes 'stop' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // stop=N ends the build on value rather than on count: build/one.php keeps the term that
  // first reaches N and then returns FALSE. inits/parms.php reads it into $pqStop, and
  // because the end is now fixed, inits/limits.php lifts the default rows= and try= caps.

  return TRUE;

?>