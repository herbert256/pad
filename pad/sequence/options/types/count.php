<?php

  // Marker file, never included: its existence is what makes 'count' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // count is in practice an action rather than an option: actions/types/count.php replaces
  // the sequence with the number of terms it holds. Nothing in the subsystem reads a
  // count= parameter, so this entry only reserves the name.

  return TRUE;

?>