<?php

  // Marker file, never included: its existence is what makes 'store' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // Nothing in the subsystem reads a store= parameter; naming a store is done with push=
  // and pull=, and exits/store/ does the writing into $pqStore. So this entry only reserves
  // the name.

  return TRUE;

?>