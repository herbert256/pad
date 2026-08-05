<?php

  // Marker file, never included: its existence is what makes 'atLeastOnce' a known
  // sequence option name - see exits/done.php, exits/info/options.php and
  // exits/store/check.php.
  //
  // Modifier for the randomize action: every entry must be drawn at least once before any
  // is drawn twice. actions/types/randomize.php reads it into $pqRandomOnce and passes it
  // to pqRandom(), where it implies duplicates. Companion options: orderly, duplicates.

  return TRUE;

?>