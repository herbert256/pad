<?php

  // Marker file, never included: its existence is what makes 'from' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // from=N is the first candidate of the range the build walks, default 1; together with
  // to=, increment= and the rows=/stop= limits it defines what build/types/type/loop.php
  // offers. On a stored or fixed term list it is a 1-based start position instead.
  // inits/parms.php reads it into $pqFrom, sole= overwrites it, and an 'a..b' form is
  // drawn once for the whole run by inits/set.php.

  return TRUE;

?>