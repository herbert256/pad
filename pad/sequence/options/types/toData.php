<?php

  // Marker file, never included: its existence is what makes 'toData' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // toData='name' publishes the finished terms as a PAD data set of that name, so later
  // tags can address them as data rather than as a sequence store. inits/parms.php reads it
  // into $pqToData and exits/data.php writes $padDataStore[$pqToData] - or, when a pop or
  // shift action ate from a pulled store, what is left of that store instead.

  return TRUE;

?>