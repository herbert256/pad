<?php

  // Marker file, never included: its existence is what makes 'single' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // Nothing in the subsystem reads a single= parameter; what narrows a run down to one
  // value is sole= (a range of one candidate) or one of the aggregate actions flagged in
  // actions/single/. So this entry only reserves the name.

  return TRUE;

?>