<?php

  // Marker file, never included: its existence is what makes 'to' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // to=N is the last candidate of the range the build walks; it defaults to PHP_INT_MAX, so
  // an unbounded run ends on rows= or stop= instead. On a stored or fixed term list it is a
  // 1-based end position. inits/parms.php reads it into $pqTo, sole= overwrites it, an
  // 'a..b' form is drawn once, and giving it lets inits/limits.php lift the default caps.

  return TRUE;

?>