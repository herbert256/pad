<?php

  // Marker file, never included: its existence is what makes 'push' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // push='name' keeps the run's terms in $pqStore under that name, for a later {pull} or
  // {resume}. inits/parms.php reads it into $pqPush; exits/store/last.php settles the key
  // into $padLastPush - a bare push reuses the pulled name, so pull/push round-trips stay on
  // one store - and exits/store/set.php does the writing. A tag pair is there to iterate its
  // terms, so it only stores when push was asked for explicitly.

  return TRUE;

?>