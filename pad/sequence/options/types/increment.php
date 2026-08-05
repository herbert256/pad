<?php

  // Marker file, never included: its existence is what makes 'increment' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // increment=N is the step between candidates in the from=/to= range, default 1;
  // inits/parms.php reads it into $pqInc. On a stored or fixed list the iterator skips that
  // many entries after each candidate instead. An 'a..b' form is drawn once for the run,
  // 'a...b' is kept in $pqRandomInc and re-rolled after every candidate.

  return TRUE;

?>