<?php

  // Marker file, never included: its existence is what makes 'duplicates' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // Modifier for the randomize action: an entry may be drawn more than once, so the result
  // can repeat itself. actions/types/randomize.php reads it into $pqRandomDuplicates and
  // passes it to pqRandom(). Companion options: orderly, atLeastOnce.

  return TRUE;

?>