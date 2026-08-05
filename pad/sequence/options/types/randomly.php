<?php

  // Marker file, never included: its existence is what makes 'randomly' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // randomly makes the build sample its range rather than walk it: every candidate is a
  // random point between from= and to=. inits/parms.php reads it into $pqRandomly,
  // build/randomly/init.php precomputes start/end/steps, and build/one.php replaces $pqLoop
  // per candidate. A strategy that cannot be sampled point-wise is built in order first and
  // sampled afterwards, by build/randomly/build/.

  return TRUE;

?>